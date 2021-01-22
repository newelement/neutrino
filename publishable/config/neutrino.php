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

    'timezone' => 'America/New_York',

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
        ['name' => 'Form', 'tag' => 'form', 'callback' => 'Newelement\\Neutrino\\Http\\Controllers\\ContentController::form_short_code' ],
        ['name' => 'Gallery', 'tag' => 'gallery', 'callback' => 'Newelement\\Neutrino\\Http\\Controllers\\ContentController::gallery_short_code' ]
    ],

    'protected_route' => 'login',

    'storage' => [
        'filesystem' => 'public',
        'filesyste_private' => 'local'
    ],

    // Enqueue scripts and css files. Files should be in the public folder.
    'enqueue_js' => [

    ],
    'enqueue_css' => [
        '/vendor/newelement/neutrino/css/theme.css',
    ],

    'enqueue_admin_js' => [

    ],

    'enqueue_admin_css' => [
        '/vendor/newelement/neutrino/css/blocks.css',
    ],

    'equeue_editor_css' => [
        'https://stackpath.bootstrapcdn.com/bootstrap/4.4.1/css/bootstrap.min.css',
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

    'editor_styles' => [
        [ "title" => "Bootstrap Buttons", "items" => [
                      [ "title"=> "Button Primary", "selector"=> "a,button", "classes"=> "btn btn-primary" ],
                    [ "title"=> "Button Secondary", "selector"=> "a,button", "classes"=> "btn btn-secondary" ],
                    [ "title"=> "Button Success", "selector"=> "a,button", "classes"=> "btn btn-success" ],
                    [ "title"=> "Button Danger", "selector"=> "a,button", "classes"=> "btn btn-danger" ],
                    [ "title"=> "Button Warning", "selector"=> "a,button", "classes"=> "btn btn-warning" ],
                    [ "title"=> "Button Info", "selector"=> "a,button", "classes"=> "btn btn-info" ],
                    [ "title"=> "Button Light", "selector"=> "a,button", "classes"=> "btn btn-light" ],
                    [ "title"=> "Button Dark", "selector"=> "a,button", "classes"=> "btn btn-dark" ],

                    [ "title"=> "Button Primary Small", "selector"=> "a,button", "classes"=> "btn btn-primary btn-sm" ],
                    [ "title"=> "Button Secondary Small", "selector"=> "a,button", "classes"=> "btn btn-secondary btn-sm" ],
                    [ "title"=> "Button Success Small", "selector"=> "a,button", "classes"=> "btn btn-success btn-sm" ],
                    [ "title"=> "Button Danger Small", "selector"=> "a,button", "classes"=> "btn btn-danger btn-sm" ],
                    [ "title"=> "Button Warning Small", "selector"=> "a,button", "classes"=> "btn btn-warning btn-sm" ],
                    [ "title"=> "Button Info Small", "selector"=> "a,button", "classes"=> "btn btn-info btn-sm" ],
                    [ "title"=> "Button Light Small", "selector"=> "a,button", "classes"=> "btn btn-light btn-sm" ],
                    [ "title"=> "Button Dark Small", "selector"=> "a,button", "classes"=> "btn btn-dark btn-sm" ],

                    [ "title"=> "Button Primary Large", "selector"=> "a,button", "classes"=> "btn btn-primary btn-lg" ],
                    [ "title"=> "Button Secondary Large", "selector"=> "a,button", "classes"=> "btn btn-secondary btn-lg" ],
                    [ "title"=> "Button Success Large", "selector"=> "a,button", "classes"=> "btn btn-success btn-lg" ],
                    [ "title"=> "Button Danger Large", "selector"=> "a,button", "classes"=> "btn btn-danger btn-lg" ],
                    [ "title"=> "Button Warning Large", "selector"=> "a,button", "classes"=> "btn btn-warning btn-lg" ],
                    [ "title"=> "Button Info Large", "selector"=> "a,button", "classes"=> "btn btn-info btn-lg" ],
                    [ "title"=> "Button Light Large", "selector"=> "a,button", "classes"=> "btn btn-light btn-lg" ],
                    [ "title"=> "Button Dark Large", "selector"=> "a,button", "classes"=> "btn btn-dark btn-lg" ],

                    [ "title"=> "Button Primary Outline", "selector"=> "a,button", "classes"=> "btn btn-outline-primary" ],
                    [ "title"=> "Button Secondary Outline", "selector"=> "a,button", "classes"=> "btn btn-outline-secondary" ],
                    [ "title"=> "Button Success Outline", "selector"=> "a,button", "classes"=> "btn btn-outline-success" ],
                    [ "title"=> "Button Danger Outline", "selector"=> "a,button", "classes"=> "btn btn-outline-danger" ],
                    [ "title"=> "Button Warning Outline", "selector"=> "a,button", "classes"=> "btn btn-outline-warning" ],
                    [ "title"=> "Button Info Outline", "selector"=> "a,button", "classes"=> "btn btn-outline-info" ],
                    [ "title"=> "Button Light Outline", "selector"=> "a,button", "classes"=> "btn btn-outline-light" ],
                    [ "title"=> "Button Dark Outline", "selector"=> "a,button", "classes"=> "btn btn-outline-dark" ],
                ]]
    ],

    'blocks' => [
        [
            'name' => 'testimonials',
            'title' => 'Testimonials',
            'icon' => 'quote-right', // FontAwesome without the fa- prefix
            'group' => true,
            'options' => [],
            'show_align_options' => true,
            'full_width' => true,
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
        ],
        [
            'name' => 'carousel',
            'title' => 'Carousel',
            'icon' => 'presentation', // FontAwesome without the fa- prefix
            'group' => true,
            'show_align_options' => true,
            'full_width' => true,
            'options' => [
                [ 'type' => 'checkbox', 'name' => 'full_width', 'label' => 'Full width (outside container)', 'value' => '1' ],
                [ 'type' => 'text', 'name' => 'height', 'label' => 'Carousel Height', 'value' => '500px' ],
                [ 'type' => 'text', 'name' => 'width', 'label' => 'Carousel Width', 'value' => '100%' ],
                [ 'type' => 'text', 'name' => 'show_slides', 'label' => 'Slides to show', 'value' => '1' ],
                [ 'type' => 'text', 'name' => 'slides_scroll', 'label' => 'Slides to scroll', 'value' => '1' ],
                [ 'type' => 'dropdown', 'name' => 'transition', 'label' => 'Transition', 'value' => 'scroll', 'options' => [
                    ['label' => 'Scroll', 'value' => 'scroll'],
                    ['label' => 'Fade', 'value' => 'fade']
                ] ],
                [ 'type' => 'text', 'name' => 'speed', 'label' => 'Speed (ms)', 'value' => '300' ],
                [ 'type' => 'checkbox', 'name' => 'autoplay', 'label' => 'Autoplay', 'value' => '1' ],
                [ 'type' => 'text', 'name' => 'autoplay_speed', 'label' => 'Autoplay speed (ms)', 'value' => '3000' ],
                [ 'type' => 'checkbox', 'name' => 'show_arrows', 'label' => 'Show Arrows', 'value' => '1' ],
                [ 'type' => 'checkbox', 'name' => 'show_dots', 'label' => 'Show paging (dots)', 'value' => '1' ]

            ],

            'fields' => [
                ['name' => 'link' , 'value' => '', 'placeholder' => 'Slide Link (optional)', 'allow_blocks' => false] ,
                ['name' => 'content', 'value' => '', 'placeholder' => 'Slide content (optional)', 'allow_blocks' => false] ,
                ['name' => 'image', 'value' => '', 'placeholder' => 'Slide Image', 'allow_blocks' => false ]
            ],

            'group_options' => [
                [ 'type' => 'dropdown', 'name' => 'background_size' , 'label' => 'Background Size', 'value' => 'cover', 'options' => [
                        ['label' => 'Cover', 'value' => 'cover'],
                        ['label' => 'Contain', 'value' => 'contain'],
                        ['label' => '100%', 'value' => '100%'],
                        ['label' => '80%', 'value' => '80%'],
                        ['label' => '60%', 'value' => '60%'],
                        ['label' => '50%', 'value' => '50%']
                    ]
                ],
                [ 'type' => 'dropdown', 'name' => 'background_position' , 'label' => 'Background Position', 'value' => 'center center', 'options' => [
                        ['label' => 'center center', 'value' => 'center center'],
                        ['label' => 'center right', 'value' => 'center right'],
                        ['label' => 'center left', 'value' => 'center left'],
                        ['label' => 'top center', 'value' => 'top center'],
                        ['label' => 'top right', 'value' => 'top right'],
                        ['label' => 'top left', 'value' => 'top left'],
                        ['label' => 'bottom center', 'value' => 'bottom center'],
                        ['label' => 'bottom right', 'value' => 'bottom right'],
                        ['label' => 'bottom left', 'value' => 'bottom left']
                    ]
                ],
                [ 'type' => 'color', 'name' => 'content_background_color', 'label' => 'Content Background Color', 'value' => '' ],
                [ 'type' => 'text', 'name' => 'content_background_opacity', 'label' => 'Content Background Opacity (0-1)', 'value' => '.45' ],
            ],

            'template'  => 'Newelement\\Neutrino\\Http\\Controllers\\BlocksController@carouselTemplate',
            'compiler' => 'Newelement\\Neutrino\\Http\\Controllers\\BlocksController@carouselCompiler'
        ],
        [
            'name' => 'gallery',
            'title' => 'Gallery',
            'icon' => 'images',
            'tag' => false,
            'contentEditable' => false,
            'template'  => 'Newelement\\Neutrino\\Http\\Controllers\\BlocksController@galleryTemplate',
            'compiler' => 'Newelement\\Neutrino\\Http\\Controllers\\BlocksController@galleryCompiler',
            'fields' => [
                ['name' => 'gallery' , 'value' => '', 'allow_blocks' => false]
            ],
            'value' => '',
            'blocks' => [],
            'group' => false,
            'show_align_options' => true,
            'full_width' => true,
            'options' => [
                    ['type' => 'dropdown', 'name' => 'theme', 'label' => 'Theme', 'value' => '', 'options' => [
                        ['label' => 'Grid', 'value' => 'grid'],
                        ['label' => 'Carousel', 'value' => 'carousel'],
                    ]
                ]
            ]
        ],
        [
            'name' => 'forms',
            'title' => 'Forms',
            'icon' => 'clipboard-list-check',
            'tag' => false,
            'contentEditable' => false,
            'template'  => 'Newelement\\Neutrino\\Http\\Controllers\\BlocksController@formTemplate',
            'compiler' => 'Newelement\\Neutrino\\Http\\Controllers\\BlocksController@formCompiler',
            'fields' => [
                ['name' => 'form' , 'value' => '', 'allow_blocks' => false]
            ],
            'value' => '',
            'blocks' => [],
            'group' => false,
            'options' => [
                [ 'type' => 'checkbox', 'name' => 'show_title', 'label' => 'Show title', 'value' => '1' ],
                ['type' => 'dropdown', 'name' => 'layout', 'label' => 'Lable layout', 'value' => 'horizontal', 'options' => [
                        ['label' => 'Horizonal', 'value' => 'horizontal'],
                        ['label' => 'Stacked', 'value' => 'stacked'],
                    ]
                ]
            ]
        ],
        [
            'name' => 'map_embed',
            'title' => 'Map Embed (basic)',
            'icon' => 'map-marker-alt',
            'tag' => false,
            'contentEditable' => false,
            'template'  => 'Newelement\\Neutrino\\Http\\Controllers\\BlocksController@mapTemplate',
            'compiler' => 'Newelement\\Neutrino\\Http\\Controllers\\BlocksController@mapCompiler',
            'fields' => [
                ['name' => 'address', 'value' => '', 'allow_blocks' => false]
            ],
            'value' => '',
            'blocks' => [],
            'group' => false,
            'show_align_options' => true,
            'full_width' => true,
            'options' => [
                [ 'type' => 'text', 'name' => 'width', 'label' => 'Width', 'value' => '100%' ],
                [ 'type' => 'text', 'name' => 'height', 'label' => 'Height', 'value' => '200' ],
                [ 'type' => 'text', 'name' => 'zoom', 'label' => 'Zoom (0-20)', 'value' => '12' ],
            ]
        ],
        [
            'name' => 'hero',
            'title' => 'Hero',
            'icon' => 'rectangle-wide',
            'tag' => false,
            'contentEditable' => false,
            'template'  => 'Newelement\\Neutrino\\Http\\Controllers\\BlocksController@heroTemplate',
            'compiler' => 'Newelement\\Neutrino\\Http\\Controllers\\BlocksController@heroCompiler',
            'fields' => [
                ['name' => 'link' , 'value' => '', 'placeholder' => 'Hero Link (optional)', 'allow_blocks' => false] ,
                ['name' => 'content', 'value' => '', 'placeholder' => 'Hero content (optional)', 'allow_blocks' => false] ,
                ['name' => 'image', 'value' => '', 'placeholder' => 'Hero Image', 'allow_blocks' => false ]
            ],
            'value' => '',
            'blocks' => [],
            'group' => false,
            'show_align_options' => true,
            'full_width' => true,
            'options' => [
                [ 'type' => 'checkbox', 'name' => 'full_width', 'label' => 'Full width (outside container)', 'value' => '1' ],
                [ 'type' => 'text', 'name' => 'height', 'label' => 'Height', 'value' => '500px' ],
                [ 'type' => 'text', 'name' => 'width', 'label' => 'Width', 'value' => '100%' ],
                [ 'type' => 'dropdown', 'name' => 'background_size' , 'label' => 'Background Size', 'value' => 'cover', 'options' => [
                    ['label' => 'Cover', 'value' => 'cover'],
                    ['label' => 'Contain', 'value' => 'contain'],
                    ['label' => '100%', 'value' => '100%'],
                    ['label' => '80%', 'value' => '80%'],
                    ['label' => '60%', 'value' => '60%'],
                    ['label' => '50%', 'value' => '50%']
                ]
            ],
            [ 'type' => 'dropdown', 'name' => 'background_position' , 'label' => 'Background Position', 'value' => 'center center', 'options' => [
                ['label' => 'center center', 'value' => 'center center'],
                ['label' => 'center right', 'value' => 'center right'],
                ['label' => 'center left', 'value' => 'center left'],
                ['label' => 'top center', 'value' => 'top center'],
                ['label' => 'top right', 'value' => 'top right'],
                ['label' => 'top left', 'value' => 'top left'],
                ['label' => 'bottom center', 'value' => 'bottom center'],
                ['label' => 'bottom right', 'value' => 'bottom right'],
                ['label' => 'bottom left', 'value' => 'bottom left']
            ]
        ],
        [ 'type' => 'dropdown', 'name' => 'content_position' , 'label' => 'Content Position', 'value' => 'center', 'options' => [
                    ['label' => 'Center', 'value' => 'center'],
                    ['label' => 'Left', 'value' => 'left'],
                    ['label' => 'Right', 'value' => 'right']
                ]
            ],
            [ 'type' => 'color', 'name' => 'content_background_color', 'label' => 'Content Background Color', 'value' => '' ],
            [ 'type' => 'text', 'name' => 'content_background_opacity', 'label' => 'Content Background Opacity (0-1)', 'value' => '.25' ],
        ]
        ],


        [
            'name' => 'divider',
            'title' => 'Dividers',
            'icon' => 'horizontal-rule',
            'tag' => false,
            'contentEditable' => false,
            'template'  => 'Newelement\\Neutrino\\Http\\Controllers\\BlocksController@dividerTemplate',
            'compiler' => 'Newelement\\Neutrino\\Http\\Controllers\\BlocksController@dividerCompiler',
            'fields' => [
                ['name' => 'link' , 'value' => '', 'placeholder' => 'Hero Link (optional)', 'allow_blocks' => false] ,
                ['name' => 'content', 'value' => '', 'placeholder' => 'Hero content (optional)', 'allow_blocks' => false] ,
                ['name' => 'image', 'value' => '', 'placeholder' => 'Divider Image', 'allow_blocks' => false ]
            ],
            'value' => '',
            'blocks' => [],
            'group' => false,
            'show_align_options' => true,
            'full_width' => true,
            'options' => [
                [ 'type' => 'checkbox', 'name' => 'full_width', 'label' => 'Full width (outside container)', 'value' => '1' ],
                [ 'type' => 'text', 'name' => 'height', 'label' => 'Height', 'value' => '24px' ],
                [ 'type' => 'text', 'name' => 'width', 'label' => 'Width', 'value' => '100%' ],
                [ 'type' => 'dropdown', 'name' => 'background_size' , 'label' => 'Background Size', 'value' => 'cover', 'options' => [
                    ['label' => 'Cover', 'value' => 'cover'],
                    ['label' => 'Contain', 'value' => 'contain'],
                    ['label' => '100%', 'value' => '100%'],
                    ['label' => '80%', 'value' => '80%'],
                    ['label' => '60%', 'value' => '60%'],
                    ['label' => '50%', 'value' => '50%']
                ]
            ],
            [ 'type' => 'dropdown', 'name' => 'background_position' , 'label' => 'Background Position', 'value' => 'center center', 'options' => [
                ['label' => 'center center', 'value' => 'center center'],
                ['label' => 'center right', 'value' => 'center right'],
                ['label' => 'center left', 'value' => 'center left'],
                ['label' => 'top center', 'value' => 'top center'],
                ['label' => 'top right', 'value' => 'top right'],
                ['label' => 'top left', 'value' => 'top left'],
                ['label' => 'bottom center', 'value' => 'bottom center'],
                ['label' => 'bottom right', 'value' => 'bottom right'],
                ['label' => 'bottom left', 'value' => 'bottom left']
            ]
        ],

        [ 'type' => 'color', 'name' => 'background_color', 'label' => 'Divider Background Color', 'value' => '' ]

        ]
        ],

        [
            'name' => 'columns',
            'title' => 'Columns',
            'icon' => 'columns',
            'tag' => false,
            'contentEditable' => false,
            'template'  => 'Newelement\\Neutrino\\Http\\Controllers\\BlocksController@columnsTemplate',
            'compiler' => 'Newelement\\Neutrino\\Http\\Controllers\\BlocksController@columnsCompiler',
            'fields' => [
                ['name' => 'content', 'value' => '', 'placeholder' => 'Slide content (optional)', 'allow_blocks' => false] ,
            ],
            'value' => '',
            'blocks' => [],
            'group' => true,
            'draggable' => false,
            'has_columns' => true,
            'show_align_options' => false,
            'full_width' => true,
            'group_options' => [
                [ 'type' => 'color', 'name' => 'background_color' , 'label' => 'Background Color', 'value' => '#ffffff' ],
                [ 'type' => 'text', 'name' => 'padding', 'label' => 'Padding', 'value' => '0px' ],
                [ 'type' => 'text', 'name' => 'margin', 'label' => 'Margin', 'value' => '0px' ],
                [ 'type' => 'dropdown', 'name' => 'column_width', 'label' => 'Column Width', 'value' => '0', 'options' => [
                        ['label' => 'Normal', 'value' => '0'],
                        ['label' => 'Larger', 'value' => '2'],
                    ]
                ],
                [ 'type' => 'dropdown', 'name' => 'width', 'label' => 'Column Width Percent', 'value' => 'auto', 'options' => [
                        ['label' => 'None', 'value' => 'auto'],
                        ['label' => '10%', 'value' => '10%'],
                        ['label' => '20%', 'value' => '20%'],
                        ['label' => '30%', 'value' => '30%'],
                        ['label' => '40%', 'value' => '40%'],
                        ['label' => '50%', 'value' => '50%'],
                        ['label' => '60%', 'value' => '60%'],
                        ['label' => '70%', 'value' => '70%'],
                        ['label' => '80%', 'value' => '80%'],
                        ['label' => '90%', 'value' => '90%'],
                        ['label' => '100%', 'value' => '100%']
                    ]
                ]

            ],
            'options' => [
                [ 'type' => 'color', 'name' => 'background_color' , 'label' => 'Background Color', 'value' => '#ffffff' ],
                [ 'type' => 'text', 'name' => 'padding', 'label' => 'Padding', 'value' => '0px' ],
                [ 'type' => 'text', 'name' => 'margin', 'label' => 'Margin', 'value' => '0px' ],
                [ 'type' => 'dropdown', 'name' => 'alignment', 'label' => 'Columns Alignment', 'value' => 'center', 'options' => [
                        ['label' => 'Center', 'value' => 'center'],
                        ['label' => 'Left', 'value' => 'flex-start'],
                        ['label' => 'Right', 'value' => 'flex-end'],
                        ['label' => 'Space Between', 'value' => 'space-between'],
                        ['label' => 'Space Around', 'value' => 'space-around'],
                        ['label' => 'Space Evenly', 'value' => 'space-evenly'],
                    ]
                ]
            ]
        ]
    ],
];
