<?php

declare(strict_types=1);

return [
    /*
     * ------------------------------------------------------------------------
     * Default Firebase project
     * ------------------------------------------------------------------------
     */

    'default' => env('FIREBASE_PROJECT', 'damkar'),

    /*
     * ------------------------------------------------------------------------
     * Firebase project configurations
     * ------------------------------------------------------------------------
     */

    'projects' => [
        'damkar' => [
            'credentials' => env('FIREBASE_CREDENTIALS', storage_path('app/firebase-credentials.json')),
            'auth' => ['tenant_id' => env('FIREBASE_AUTH_TENANT_ID')],
            'firestore' => [],
            'database' => ['url' => env('FIREBASE_DATABASE_URL')],
            'storage' => ['default_bucket' => env('FIREBASE_STORAGE_DEFAULT_BUCKET')],
            'cache_store' => env('FIREBASE_CACHE_STORE', 'file'),
            'logging' => [
                'http_log_channel' => env('FIREBASE_HTTP_LOG_CHANNEL'),
                'http_debug_log_channel' => env('FIREBASE_HTTP_DEBUG_LOG_CHANNEL'),
            ],
            'http_client_options' => [
                'proxy' => env('FIREBASE_HTTP_CLIENT_PROXY'),
                'timeout' => env('FIREBASE_HTTP_CLIENT_TIMEOUT'),
                'guzzle_middlewares' => [],
            ],
        ],

        'pmi' => [
            'credentials' => env('FIREBASE_PMI_CREDENTIALS', storage_path('app/firebase-pmi-credentials.json')),
            'auth' => ['tenant_id' => env('FIREBASE_PMI_AUTH_TENANT_ID')],
            'firestore' => [],
            'database' => ['url' => env('FIREBASE_PMI_DATABASE_URL')],
            'storage' => ['default_bucket' => env('FIREBASE_PMI_STORAGE_DEFAULT_BUCKET')],
            'cache_store' => env('FIREBASE_CACHE_STORE', 'file'),
            'logging' => [
                'http_log_channel' => env('FIREBASE_HTTP_LOG_CHANNEL'),
                'http_debug_log_channel' => env('FIREBASE_HTTP_DEBUG_LOG_CHANNEL'),
            ],
            'http_client_options' => [
                'proxy' => env('FIREBASE_HTTP_CLIENT_PROXY'),
                'timeout' => env('FIREBASE_HTTP_CLIENT_TIMEOUT'),
                'guzzle_middlewares' => [],
            ],
        ],

        'gmci' => [
            'credentials' => env('FIREBASE_GMCI_CREDENTIALS', storage_path('app/firebase-gmci-credentials.json')),
            'auth' => ['tenant_id' => env('FIREBASE_GMCI_AUTH_TENANT_ID')],
            'firestore' => [],
            'database' => ['url' => env('FIREBASE_GMCI_DATABASE_URL')],
            'storage' => ['default_bucket' => env('FIREBASE_GMCI_STORAGE_DEFAULT_BUCKET')],
            'cache_store' => env('FIREBASE_CACHE_STORE', 'file'),
            'logging' => [
                'http_log_channel' => env('FIREBASE_HTTP_LOG_CHANNEL'),
                'http_debug_log_channel' => env('FIREBASE_HTTP_DEBUG_LOG_CHANNEL'),
            ],
            'http_client_options' => [
                'proxy' => env('FIREBASE_HTTP_CLIENT_PROXY'),
                'timeout' => env('FIREBASE_HTTP_CLIENT_TIMEOUT'),
                'guzzle_middlewares' => [],
            ],
        ],
    ],
];