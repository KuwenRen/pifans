<?php

return [
    'autoload' => false,
    'hooks' => [
        'wipecache_after' => [
            'tinymce',
        ],
        'set_tinymce' => [
            'tinymce',
        ],
    ],
    'route' => [],
    'priority' => [],
    'domain' => '',
];
