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
    $data['scholarly_repository_publications']['title'] = [
        'title' => t('Title'),
        'help' => t('Title'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['scholarly_repository_publications']['institution_title'] = [
        'title' => t('Institution Title'),
        'help' => t('Institution Title'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['scholarly_repository_publications']['publication_title'] = [
        'title' => t('Publication Title'),
        'help' => t('Publication Title'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['scholarly_repository_publications']['author'] = [
        'title' => t('Author'),
        'help' => t('Author'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['scholarly_repository_publications']['author_display'] = [
        'title' => t('Author Display'),
        'help' => t('Author Display'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['scholarly_repository_publications']['document_type'] = [
        'title' => t('Document Type'),
        'help' => t('Document Type'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['scholarly_repository_publications']['context_key'] = [
        'title' => t('Context Key'),
        'help' => t('Context Key'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['scholarly_repository_publications']['download_format'] = [
        'title' => t('Download Format'),
        'help' => t('Download Format'),
        'field' => [
            'id' => 'standard',
        ],
    ];
    $data['scholarly_repository_publications']['peer_reviewed'] = [
        'title' => t('Peer Reviewed'),
        'help' => t('Peer Reviewed'),
        'field' => [
            'id' => 'boolean',
        ],
    ];
    $data['scholarly_repository_publications']['publication_date'] = [
        'title' => t('Publication Date'),
        'help' => t('Publication Date'),
        'field' => [
            'id' => 'date',
        ],
    ];
    $data['scholarly_repository_publications']['publication_link'] = [
        'title' => t('Publication Link'),
        'help' => t('Publication Link'),
        'field' => [
            'id' => 'url',
        ],
    ];
    $data['scholarly_repository_publications']['url'] = [
        'title' => t('URL'),
        'help' => t('URL'),
        'field' => [
            'id' => 'url',
        ],
    ];
    $data['scholarly_repository_publications']['download_link'] = [
        'title' => t('Download Link'),
        'help' => t('Download Link'),
        'field' => [
            'id' => 'url',
        ],
    ];

    return $data;
}
