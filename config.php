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
    'log_path' => '/tmp/pimusic/log',
    'cache_path' => '/tmp/pimusic/cache',
];