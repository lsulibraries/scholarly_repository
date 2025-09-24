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
     * To silence the warning: Creation of dynamic property $where is deprecated
     */
    protected $where;

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
    public $context;

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
    public function build(ViewExecutable $view)
    {
        $view->initPager();
    }

    /**
     * {@inheritdoc}
     */
    public function execute(ViewExecutable $view)
    {
        // Pager config
        $offset = $view->getOffset();
        $items_per_page = $view->getItemsPerPage();
        $current_page = $view->getCurrentPage();

        $chunk_size = 25;
        $start = $offset + ($items_per_page * $current_page);

        try {
            $data = [];

            for ($i = 0; count($data) < $items_per_page; $i++) {
                $url = Url::fromUri('internal:/api-proxy/scholarly_repository_api_proxy', [
                    'query' => [
                        '_api_proxy_uri' => '/do/search/results/json' .
                            '?q=' . ($this->search_terms ?: "LSU") .
                            "&context=" . $this->context .
                            "&start=" . $start + ($i * $chunk_size) .
                            "&sort=" . $this->sort_by
                    ],
                    'absolute' => TRUE,
                ])->toString(TRUE)->getGeneratedUrl();

                $this->logger->debug($url);

                $response = $this->client->request(
                    'GET',
                    $url,
                );

                $json = json_decode($response->getBody()->getContents(), TRUE);
                $view->pager->total_items = $json['num_found'];

                $data = array_merge($data, $json['docs']);
            }

            $data = array_slice($data, 0, $items_per_page);

            $index = 0;
            foreach ($data as $publication) {
                $row['id'] = $publication['context_key'];
                $row['title'] = $publication['title'];
                $row['institution_title'] = $publication['institution_title'];
                $row['publication_title'] = $publication['publication_title'];
                $row['author'] = isset($publication['author']) ? implode(",", $publication['author']) : "";
                $row['author_display'] = isset($publication['author_display']) ? implode(",", $publication['author_display']) : "";
                $row['document_type'] = isset($publication['document_type']) ? implode(",", $publication['document_type']) : "";
                $row['download_format'] = $publication['download_format'] ?: '';
                $row['peer_reviewed'] = $publication['peer_reviewed'];
                $row['publication_date'] = $publication['publication_date'];
                $row['publication_link'] = $publication['publication_link'];
                $row['download_link'] = $publication['publication_link'];
                $row['url'] = $publication['url'];
                $row['search_term'] = $this->search_terms;

                $row['index'] = $index++;

                $view->result[] = new ResultRow($row);
            }

            $view->pager->updatePageInfo();
            $view->pager->postExecute($view->result);
            $view->total_rows = $json['num_found'];
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
