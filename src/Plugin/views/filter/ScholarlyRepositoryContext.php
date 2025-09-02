<?php

namespace Drupal\scholarly_repository\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\scholarly_repository\Plugin\views\ScholarlyRepositoryHandlerTrait;
use Drupal\views\Plugin\views\filter\Equality;

/**
 * Filter by publication context.
 * 
 * @ViewsFilter("scholarly_repository_context")
 */
class ScholarlyRepositoryContext extends Equality
{

    use ScholarlyRepositoryHandlerTrait;

    /**
     * Provide a simple textfield for equality.
     */
    protected function valueForm(&$form, FormStateInterface $form_state)
    {
        parent::valueForm($form, $form_state);

        $form['value'] = [
            '#type' => 'select',
            '#title' => $this->t('Value'),
            '#default_value' => $this->value,
            '#options' => [
                '1' => $this->t('All Repositories'),
                '8403704' => $this->t('LSU Scholarly Repository'),
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function defineOptions()
    {
        $options = parent::defineOptions();

        $options['value'] = ['default' => '1'];

        return $options;
    }

    /**
     * {@inheritdoc}
     */
    public function operatorOptions()
    {
        return [
            '=' => $this->t('Is equal to'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function adminSummary()
    {
        switch ($this->value) {
            case '1':
                return $this->operator . ' ' . 'All Repositories ';

            default:
                return $this->operator . ' ' . 'LSU Scholarly Repository';
        }
    }

    /**
     * {@inheritdoc}
     */
    public function query()
    {
        $query = $this->getQuery();
        $query->context =  $this->value;
    }
}
