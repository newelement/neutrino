<?php
return [
    'controllers' => [
        'namespace' => 'Newelement\\Neutrino\\Http\\Controllers',
    ],

    'multilingual' => [
        'enabled' => false,
        'default' => 'en',
        'locales' => [
            'en',
        ],
    ],

    'admin_menu_items' => [
        /*[
            'slot' => 4,
            'url' => '/admin/locations',
            'parent_title' => 'Locations',
            'named_route' => 'neutrino.locations',
            'fa-icon' => 'fa-map-marked',
            'children' => [
                [ 'url' => '/admin/locations', 'title' => 'All Locations' ],
                [ 'url' => '/admin/location', 'title' => 'Create Location' ],
            ],
        ],*/
    ],

    'short_codes' => [
        ['name' => 'Form', 'tag' => 'form', 'callback' => 'Newelement\\Neutrino\\Http\\Controllers\\ContentController::form_short_code' ]
    ],

    'protected_route' => 'login',

    'storage' => [
        'filesystem' => 'public'
    ],

    // Enqueue scripts and css files. Files should be in the public folder.
    'enqueue_js' => [

    ],
    'enqueue_css' => [

    ],

    'enqueue_admin_js' => [

    ],
    'enqueue_admin_css' => [
        '/vendor/newelement/neutrino/css/blocks.css',
    ],

    'pagination_limits' => [
        'default' => 20,
        'entries' => 20,
        'post' => 20,
        'pages' => 20,
        'taxonomies' => 20,
        'events' => 20
    ],


    'ordering' => [
        'default' => [
            'order' => 'title',
            'sort' => 'asc'
        ],
        'entry_types' => [
            'default' => [
                'order' => 'title',
                'sort' => 'asc'
            ],
        ],
        'entries' => [
            'default' => [
                'order' => 'created_at',
                'sort' => 'desc'
            ],
        ],
        'taxonomies' => [
            'default' => [
                'order' => 'title',
                'sort' => 'asc'
            ],
        ],
        'taxonomy_entries' => [
            'default' => [
                'order' => 'created_at',
                'sort' => 'desc'
            ],
        ]
    ],


    'media' => [
        'image_sizes' => [
            'large' => 2000,
            'medium' => 1200,
            'small' => 600,
            'thumb' => 280,
        ],
        'thumb_crop' => 'square',
    ],

    'calendar_date_format' => '',

    'default_register_role' => 'guest',
    'default_register_redirect' => '/',

    'blocks' => [
        [
            'name' => 'testimonials',
            'title' => 'Testimonials',
            'icon' => 'quote-right', // FontAwesome without the fa- prefix
            'group' => true,
            'options' => [],

            'fields' => [
                ['name' => 'quote' , 'value' => '', 'placeholder' => 'Quote', 'allow_blocks' => false] ,
                ['name' => 'author', 'value' => '', 'placeholder' => 'Author, Position', 'allow_blocks' => false] ,
                ['name' => 'image', 'value' => '', 'placeholder' => 'Author Image', 'allow_blocks' => true ]
            ],

            'group_options' => [
                [ 'type' => 'color', 'name' => 'background_color' , 'label' => 'Background Color', 'value' => '' ],
                [ 'type' => 'text', 'name' => 'padding', 'label' => 'Padding', 'value' => '0px' ],
                [ 'type' => 'dropdown', 'name' => 'text_alignment', 'label' => 'Text Alignment', 'value' => '', 'options' => [
                        ['label' => 'Left', 'value' => 'left'],
                    ]
                ],
            ],

            'template'  => 'Newelement\\Neutrino\\Http\\Controllers\\BlocksController@testimonial',
            'compiler' => 'Newelement\\Neutrino\\Http\\Controllers\\BlocksController@testimonialCompiler'
        ]
    ],
];
