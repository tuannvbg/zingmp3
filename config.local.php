<?php

return [
    'mopidy' => [
        'url' => 'http://125.234.98.126:6680/mopidy/rpc',
    ],
    'parser' => [
        'plugins' => ['Zingmp3'],
    ],
    'queue' => [

    ],
    'slack' => [
        'webhook_url' => 'https://hooks.slack.com/services/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',
    ],
    'log_path' => '/tmp/pimusic/log',
    'cache_path' => '/tmp/pimusic/cache',
];