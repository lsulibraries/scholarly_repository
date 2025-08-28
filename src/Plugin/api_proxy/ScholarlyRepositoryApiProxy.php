<?php

namespace Drupal\scholarly_repository\Plugin\api_proxy;

use Drupal\api_proxy\Plugin\HttpApiPluginBase;
use Drupal\Core\Form\SubformStateInterface;
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

    // use HttpApiCommonConfigs;

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
        // Modify the response from the API.
        // A common problem is to remove the Transfer-Encoding header.
        $response->headers->remove('transfer-encoding');
        return $response;
    }
}
