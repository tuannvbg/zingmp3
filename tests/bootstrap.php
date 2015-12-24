<?php

if (!defined('BASE_PATH'))
    define('BASE_PATH', dirname(dirname(__FILE__)));

define('BOT_PREFIX', 'PiMusic');

require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/lib/App.php';

App::create();