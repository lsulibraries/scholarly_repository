<?php

namespace Drupal\scholarly_repository\Plugin\views\query;

use Drupal\Core\Url;
use Drupal\views\Plugin\views\query\QueryPluginBase;
use Drupal\views\ResultRow;
use Drupal\views\ViewExecutable;
use GuzzleHttp\ClientInterface;
use Psr\Container\ContainerInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Stringable;

/**
 * Scholarly Repository views query plugin which wraps calls to the LSU Scholarly Repository API in order to
 * expose the results to views.
 *
 * @ViewsQuery(
 *   id = "scholarly_repository_query",
 *   title = @Translation("Scholarly Repository Query"),
 *   help = @Translation("Query against the LSU Scholarly Repository API.")
 * )
 */
class ScholarlyRepositoryQuery extends QueryPluginBase
{
    use LoggerTrait;

    /**
     * The HTTP client.
     *
     * @var \GuzzleHttp\ClientInterface
     */
    private $client;

    /**
     * The logging channel to use.
     *
     * @var \Psr\Log\LoggerInterface|null
     */
    protected $logger;

    /**
     * Search terms
     * 
     * @var string|null
     */
    public $search_terms;

    /**
     * Search operator
     * 
     * @var string|null
     */
    public $search_match;

    /**
     * Sort by
     * 
     * @var string|null
     */
    public $sort_by;

    /**
     * Status
     * 
     * @var string|null
     */
    public $status;

    /**
     * {@inheritdoc}
     */
    public function __construct(array $configuration, $plugin_id, $plugin_definition, ClientInterface $client, LoggerInterface $logger)
    {
        parent::__construct($configuration, $plugin_id, $plugin_definition, $client);
        $this->client = $client;
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public static function create(ContainerInterface $container, array $configuration, $plugin_id, $plugin_definition): self
    {
        return new static(
            $configuration,
            $plugin_id,
            $plugin_definition,
            $container->get('http_client'),
            $container->get('logger.channel.scholarly_repository')
        );
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ViewExecutable $view)
    {
        try {
            $url = Url::fromUri('internal:/api-proxy/scholarly_repository_api_proxy', [
                'query' => [
                    '_api_proxy_uri' => '/do/search' .
                        '?q=' . $this->search_terms .
                        "&context=" . 1 .
                        "&start=" . 0 . // @TODO Modify items per page. Seems to be fixed to 25
                        "&sort=" . $this->sort_by
                ],
                'absolute' => TRUE,
            ])->toString(TRUE)->getGeneratedUrl();

            $response = $this->client->request(
                'GET',
                $url,
            );

            $data = json_decode($response->getBody()->getContents(), TRUE);
            $this->logger->log('debug', $data);

            $offset = $view->getOffset();
            $items_per_page = $view->getItemsPerPage();
            $currentPage = $view->getCurrentPage();

            $index = 0;
            foreach ($data as $publication) {
                $row['id'] = $publication['id'];
                $row['title'] = $publication['title'];
                $row['url'] = $publication['url'];
                $row['search_term'] = $this->search_terms;

                $row['index'] = $index++;

                $view->result[] = new ResultRow($row);
            }

            $view->pager->updatePageInfo();
            $view->pager->postExecute($view->result);
            $view->total_rows = $view->pager->getTotalItems();
        } catch (\Throwable $th) {
            $this->error($th->getMessage());
        }
    }

    /**
     * Views core assume an SQL-query backend. 
     * To mitigate that, we need to implement two methods which will, in a sense, ignore core Views as a way to work around this limitation.
     */
    public function ensureTable($table, $relationship = NULL)
    {
        return '';
    }

    public function addField($table, $field, $alias = '', $params = array())
    {
        return $field;
    }

    public function log($level, string|Stringable $message, array $context = []): void
    {
        $this->logger->log($level, $message, $context);
    }
}
