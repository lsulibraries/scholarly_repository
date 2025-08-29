<?php

namespace Drupal\scholarly_repository\Plugin\views\filter;

use Drupal\scholarly_repository\Plugin\views\ScholarlyRepositoryHandlerTrait;
use Drupal\views\Plugin\views\filter\InOperator;

/**
 * Filter by publication context.
 * 
 * @ViewsFilter("scholarly_repository_context")
 */
class ScholarlyRepositoryContext extends InOperator
{

    use ScholarlyRepositoryHandlerTrait;

    protected $valueFormType = 'radios';

    /**
     * {@inheritdoc}
     */
    public function getValueOptions()
    {
        $this->valueOptions = [
            $this->t('LSU Scholarly Repository'),
            $this->t('All Repositories'),
        ];

        return $this->valueOptions;
    }

    /**
     * {@inheritdoc}
     */
    public function operators()
    {
        $operators = [
            'in' => [
                'title' => $this->t('Is one of'),
                'short' => $this->t('in'),
                'short_single' => $this->t('='),
                'method' => 'opSimple',
                'values' => 1,
            ],
        ];
        return $operators;
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $query = $this->getQuery();
        if (isset($this->value['all'])) unset($this->value['all']);
        $query->status = implode(',', $this->value);
    }
}
