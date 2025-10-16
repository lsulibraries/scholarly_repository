<?php

namespace Drupal\scholarly_repository\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\scholarly_repository\Plugin\views\ScholarlyRepositoryHandlerTrait;
use Drupal\views\Plugin\views\filter\BooleanOperator;

/**
 * Filter by searching Scholarly Repository full-text index.
 * 
 * @ViewsFilter("scholarly_repository_search_terms")
 */
class ScholarlyRepositorySearchTerms extends BooleanOperator
{
    use ScholarlyRepositoryHandlerTrait;

    /**
     * {@inheritdoc}
     */
    public function showOperatorForm(&$form, FormStateInterface $form_state)
    {
        parent::showOperatorForm($form, $form_state);

        if (!empty($form['operator'])) {
            $form['operator']['#description'] = $this->t('The operator to apply between search terms.');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function operatorOptions($which = 'title')
    {
        $options = [];
        foreach ($this->operators() as $id => $info) {
            $options[$id] = $info[$which];
        }

        return $options;
    }

    /**
     * Returns information about the available operators for this filter.
     *
     * @return array[]
     *   An associative array mapping operator identifiers to their information.
     *   The operator information itself is an associative array with the
     *   following keys:
     *   - title: The translated title for the operator.
     *   - short: The translated short title for the operator.
     *   - values: The number of values the operator requires as input.
     */
    public function operators()
    {
        return [
            'and' => [
                'title' => $this->t('Contains all of these words'),
                'short' => $this->t('and'),
                'values' => 1,
            ],
            'or' => [
                'title' => $this->t('Contains any of these words'),
                'short' => $this->t('or'),
                'values' => 1,
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function defineOptions()
    {
        $options = parent::defineOptions();

        $options['operator']['default'] = 'and';

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    protected function valueForm(&$form, FormStateInterface $form_state)
    {
        parent::valueForm($form, $form_state);

        $form['value'] = [
            '#type' => 'textfield',
            '#title' => $this->t('Value'),
            '#default_value' => $this->value,
        ];
    }

    public function query()
    {
        $query = $this->getQuery();

        if (!$this->value) return;

        // Pass any data you need to your query plugin.
        $query->search_terms = $this->value;
        $query->search_match = $this->operator;
    }
}
