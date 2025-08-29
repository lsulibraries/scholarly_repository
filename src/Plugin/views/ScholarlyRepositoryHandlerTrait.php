<?php

namespace Drupal\scholarly_repository\Plugin\views;

use Drupal\scholarly_repository\Plugin\views\query\ScholarlyRepositoryQuery;

trait ScholarlyRepositoryHandlerTrait
{

    /**
     * Retrieves the query plugin.
     *
     * @return \Drupal\scholarly_repository\Plugin\views\query\ScholarlyRepositoryQuery|null
     *   The query plugin, or NULL if there is no query or it is not ScholarlyRepositoryQuery
     *   query.
     */
    public function getQuery()
    {
        $query = $this->query ?? $this->view->query ?? NULL;
        return $query instanceof ScholarlyRepositoryQuery ? $query : NULL;
    }
}
