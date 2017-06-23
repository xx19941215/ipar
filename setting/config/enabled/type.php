<?php
$this
    ->set('type', [
        'rqt' => 1,
        'feature' => 2,
        'product' => 3,
        'invent' => 4,
        'sketch' => 5,
        'appearance' => 6,
        'solved' => 7,
        'improving' => 8,
        'solution' => 9,
        'property' => 10,
        'creative' => 11,
        'idea' => 12,
        'article' => 13,
        'group' => 14,
        'company' => 15
    ])
    ->set('type_relation', [
        'entity' => [
            'rqt',
            'feature',
            'product',
            'invent',
            'sketch',
            'appearance',
        ],
        'solution' => [
            'rqt',
            'idea',
            'product',
            'invent'
        ],
        'propertiy' => [
            'feature',
            'solved',
            'improving'
        ],
        'creative' => [
            'rqt',
            'feature',
            'sketch',
            'appearance'
        ],
        'relation' => [
            'solution',
            'property',
            'creative'
        ],
        'article' => [
            'article'
        ],
        'group' => [
            'group',
            'company'
        ]
    ]);
