<?php

namespace Drupal\scholarly_repository\Plugin\views\sort;

use Drupal\scholarly_repository\Plugin\views\ScholarlyRepositoryHandlerTrait;
use Drupal\views\Plugin\views\sort\SortPluginBase;

/**
 * Provides a sort plugin for ScholarlyRepository views.
 *
 * @ViewsSort("scholarly_repository_sort_by")
 */
class ScholarlyRepositorySortBy extends SortPluginBase
{

    use ScholarlyRepositoryHandlerTrait;

    /**
     * {@inheritdoc}
     */
    public function defineOptions()
    {
        $options = parent::defineOptions();

        $options['order']['default'] = 'score';

        return $options;
    }

    /**
     * Provide a list of options for the default sort form.
     *
     * Should be overridden by classes that don't override sort_form
     */
    protected function sortOptions()
    {
        return [
            'date' => $this->t('Sort by publication date'),
            'score' => $this->t('Sort by relevance'),
        ];
    }

    /**
     * Display whether or not the sort order is ascending or descending.
     */
    public function adminSummary()
    {
        if (!empty($this->options['exposed'])) {
            return $this->t('Exposed');
        }
        switch ($this->options['order']) {
            case 'date';
                return $this->t('Publication date');

            case 'score':
            default:
                return $this->t('Relevance');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $this->getQuery()->sort_by = $this->options['order'];
    }
}
