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
        if ($response->getStatusCode() !== 200) {
            return $this->forward($this->request, $this->uri);
        }
        // Modify the response from the API.
        // A common problem is to remove the Transfer-Encoding header.
        $response->headers->remove('transfer-encoding');
        return $response;
    }

    /**
     * {@inheritdoc}
     */
    public function forward(Request $request, string $uri): Response
    {
        $this->request = $request;
        $this->uri = $uri;
        return parent::forward($request, $uri);
    }
}
