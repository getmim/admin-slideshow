<?php

return [
    '__name' => 'admin-slideshow',
    '__version' => '0.0.1',
    '__git' => 'git@github.com:getmim/admin-slideshow.git',
    '__license' => 'MIT',
    '__author' => [
        'name' => 'Iqbal Fauzi',
        'email' => 'iqbalfawz@gmail.com',
        'website' => 'http://iqbalfn.com/'
    ],
    '__files' => [
        'modules/admin-slideshow' => ['install','update','remove'],
        'theme/admin/slideshow' => ['install','update','remove'],
        'theme/admin/form/field/slideshow.phtml' => ['install','update','remove'],
        'theme/admin/static/js/slideshow.js' => ['install','update','remove'],
        'theme/admin/static/css/slideshow.css' => ['install','update','remove']
    ],
    '__dependencies' => [
        'required' => [
            [
                'admin' => NULL
            ],
            [
                'lib-form' => NULL
            ],
            [
                'lib-formatter' => NULL
            ],
            [
                'lib-pagination' => NULL
            ],
            [
                'slideshow' => NULL
            ]
        ],
        'optional' => []
    ],
    'autoload' => [
        'classes' => [
            'AdminSlideshow\\Controller' => [
                'type' => 'file',
                'base' => 'modules/admin-slideshow/controller'
            ]
        ],
        'files' => []
    ],
    'routes' => [
        'admin' => [
            'adminSlideshow' => [
                'path' => [
                    'value' => '/component/slideshow'
                ],
                'method' => 'GET',
                'handler' => 'AdminSlideshow\\Controller\\Slideshow::index'
            ],
            'adminSlideshowEdit' => [
                'path' => [
                    'value' => '/component/slideshow/(:id)',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET|POST',
                'handler' => 'AdminSlideshow\\Controller\\Slideshow::edit'
            ],
            'adminSlideshowRemove' => [
                'path' => [
                    'value' => '/component/slideshow/(:id)/remove',
                    'params' => [
                        'id'  => 'number'
                    ]
                ],
                'method' => 'GET',
                'handler' => 'AdminSlideshow\\Controller\\Slideshow::remove'
            ]
        ]
    ],
    'adminUi' => [
        'sidebarMenu' => [
            'items' => [
                'component' => [
                    'label' => 'Component',
                    'icon' => '<i class="fas fa-puzzle-piece"></i>',
                    'priority' => 0,
                    'children' => [
                        'slideshow' => [
                            'label' => 'Slideshow',
                            'icon'  => '<i></i>',
                            'route' => ['adminSlideshow'],
                            'perms' => 'manage_slideshow'
                        ]
                    ]
                ]
            ]
        ]
    ],
    'libForm' => [
        'forms' => [
            'admin.component-slideshow.index' => [
                'q' => [
                    'label' => 'Search',
                    'type' => 'search',
                    'nolabel' => true,
                    'rules' => []
                ]
            ],
            'admin.component-slideshow.edit' => [
                'name' => [
                    'label' => 'Name',
                    'type' => 'text',
                    'rules' => [
                        'required' => true,
                        'empty' => false
                    ]
                ],
                'images' => [
                    'type' => 'slideshow',
                    'label' => 'Images',
                    'rules' => [
                        'required' => true,
                        'empty' => false,
                        'json' => true
                    ]
                ]
            ]
        ]
    ]
];