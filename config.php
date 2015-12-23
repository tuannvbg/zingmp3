<?php

return [
    'mopidy' => [
        'url' => 'http://125.234.98.126:6680/mopidy/rpc',
    ],
    'parser' => [
        'plugins' => ['Zingmp3', 'Nhaccuatui'],
    ],
    'queue' => [

    ],
    'slack' => [
        'webhook_url' => 'https://hooks.slack.com/services/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',
    ],
    'log_path' => '/tmp/pimusic/log',
    'cache_path' => '/tmp/pimusic/cache',
];