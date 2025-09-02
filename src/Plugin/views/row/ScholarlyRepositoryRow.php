<?php

namespace Drupal\scholarly_repository\Plugin\views\row;

use Drupal\views\Plugin\views\row\RowPluginBase;

/**
 * Renders API data in a custom Twig template.
 *
 * @ViewsRow(
 *   id = "publication",
 *   title = @Translation("Publication"),
 *   help = @Translation("Render Scholarly Repository API results using a Twig template.")
 * )
 */
class ScholarlyRepositoryRow extends RowPluginBase
{

    /**
     * {@inheritdoc}
     */
    public function render($row)
    {
        return [
            '#theme' => 'publication',
            '#title' => $row->title,
            '#institution_title' => $row->institution_title,
            '#publication_title' => $row->publication_title,
            '#author' => $row->author,
            '#author_display' => $row->author_display,
            '#document_type' => $row->document_type,
            '#download_format' => $row->download_format,
            '#peer_reviewed' => $row->peer_reviewed,
            '#publication_date' => $row->publication_date,
            '#publication_link' => $row->publication_link,
            '#download_link' => $row->download_link,
            '#url' => $row->url,
            '#search_term' => $row->search_term,
            '#view' => $this->view,
        ];
    }
}
