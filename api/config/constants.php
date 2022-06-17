<?php
return [
    'pageSize' => 4,
    'rateLimit' => 60,
    'cache' => [
        'posts' => [
            'paginationPrefixRoot' => 'posts-pagination-first',
            // the business requirement is 2-3 posts per hour so we will anticipate 2.5 posts per hour or 1 every 24 minutes that is 1440 seconds
            // this needs to be revaluated as more and more users are onboarded to the system as blog post publishers which will bring the ttl down even further
            'ttlMain' => 1440,
            'ttlFile' => 60,
            'paginationPrefix' => 'posts-pagination-',
            'singleItemPrefix' => 'posts-single-',
        ],
        'users' => [
            'userFromFirebasePrefix' => 'users-firebase-',
            'ttlUserFromFirebase' => 3600,
        ]
    ],
    'jwt' => [
        'firebase' => [
            'isVerifyToken' => env('IS_FIREBASE_VERIFY_TOKEN', 'default'),
        ]
    ]
];
