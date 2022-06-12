<?php
return [
    'pageSize' => 2,
    'cache' => [
        'keys' => [
            'posts' => [
                'paginationPrefixRoot' => 'posts-pagination-main',
                // the business requirement is 2-3 posts per hour so we will anticipate 2.5 posts per hour or 1 every 24 minutes that is 1440 seconds
                // this needs to be revaluated as more and more users are onboarded to the system as blog post publishers which will bring the ttl down even further
                'ttl' => 1440,
                'paginationPrefix' => 'posts-pagination-',
                'singleItemPrefix' => 'posts-single-',
            ]
        ]
    ]
];
