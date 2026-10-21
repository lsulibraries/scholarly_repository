<?php

namespace Drupal\scholarly_repository\Plugin\api_proxy;

use Drupal\api_proxy\Plugin\api_proxy\HttpApiCommonConfigs;
use Drupal\api_proxy\Plugin\HttpApiPluginBase;
use Drupal\Core\Form\SubformStateInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * The Scholarly Repository API.
 *
 * @HttpApi(
 *   id = "scholarly_repository_api_proxy",
 *   label = @Translation("Scholarly Repository API"),
 *   description = @Translation("Proxies requests to the LSU Scholarly Repository API."),
 *   serviceUrl = "https://repository.lsu.edu",
 * )
 */
final class ScholarlyRepositoryApiProxy extends HttpApiPluginBase
{

    use HttpApiCommonConfigs;

    /**
     * Caching the request and URI for retries
     */

    /**
     * Request
     * 
     * @var \Symfony\Component\HttpFoundation\Request
     */
    private Request $request;

    /**
     * URI where the request is being sent
     * 
     * @var string
     */
    private string $uri;

    /**
     * Maximum number of HTTP attempts, including the first request.
     */
    private const MAX_ATTEMPTS = 3;

    /**
     * HTTP statuses that may indicate a transient upstream failure.
     */
    private const RETRYABLE_STATUSES = [429, 502, 503, 504];

    /**
     * Number of attempts made so far.
     */
    private int $attempts = 0;

    /**
     * {@inheritdoc}
     */
    public function addMoreConfigurationFormElements(array $form, SubformStateInterface $form_state): array
    {
        return $form;
    }

    /**
     * {@inheritdoc}
     */
    public function postprocessOutgoing(Response $response): Response
    {
        $status = $response->getStatusCode();

        if (
            in_array($status, self::RETRYABLE_STATUSES, true) &&
            $this->attempts < self::MAX_ATTEMPTS - 1
        ) {
            $this->attempts++;

            usleep(250000 * (2 ** ($this->attempts - 1)));

            return $this->forward($this->request, $this->uri);
        }

        if ($status !== 200) {
            \Drupal::logger('scholarly_repository')->error(
                'Upstream request failed after @attempts attempt(s). HTTP status: @status. Response: @response',
                [
                    '@attempts' => $this->attempts + 1,
                    '@status' => $status,
                    '@response' => $response->getContent(),
                ]
            );

            return $response;
        }

        $response->headers->remove('transfer-encoding');

        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function forward(Request $request, string $uri): Response
    {
        $isRetry = $this->attempts > 0;
        if (!$isRetry) {
            $this->attempts = 0;
        }
        $this->request = $request;
        $this->uri = $uri;
        return parent::forward($request, $uri);
    }
}
