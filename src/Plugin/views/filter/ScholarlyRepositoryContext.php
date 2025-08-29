<?php

namespace Drupal\scholarly_repository\Plugin\views\filter;

use Drupal\Core\Form\FormStateInterface;
use Drupal\scholarly_repository\Plugin\views\ScholarlyRepositoryHandlerTrait;
use Drupal\views\Plugin\views\filter\FilterPluginBase;

/**
 * Filter by publication context (LSU Scholarly Repository or All Repositories).
 * 
 * @ViewsFilter("scholarly_repository_context")
 */
class ScholarlyRepositoryContext extends FilterPluginBase
{
    use ScholarlyRepositoryHandlerTrait;
}
