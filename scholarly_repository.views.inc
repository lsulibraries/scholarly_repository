<?php

/**
 * Implements hook_views_data().
 */
function scholarly_repository_views_data()
{
    $data = [];

    // Base data.
    $data['scholarly_repository_publications']['table']['group'] = t('Scholarly Repository');
    $data['scholarly_repository_publications']['table']['base'] = [
        'title' => t('Scholarly Repository Publication'),
        'help' => t('Scholarly repository publication data provided by the Scholarly Repository API.'),
        'query_id' => 'scholarly_repository_query',
    ];

    // Filters
    $data['scholarly_repository_publications']['scholarly_repository_search'] = [
        'group' => t('Search'),
        'title' => t('Scholarly Repository Search'),
        'help' => t('Filter by searching publication full-text.'),
        'filter' => [
            'id' => 'scholarly_repository_search_terms',
        ],
    ];

    // Sort
    $data['scholarly_repository_publications']['scholarly_repository_sort_by'] = [
        'title' => t('Scholarly Repository Sort By'),
        'help' => t('Sorting of returned publications.'),
        'sort' => [
            'id' => 'scholarly_repository_sort_by',
        ],
    ];

    // Fields
    $data['scholarly_repository_publications']['id'] = [
        'title' => t('ID'),
        'help' => t('Publication ID'),
        'field' => [
            'id' => 'standard',
        ],
    ];


    return $data;
}
