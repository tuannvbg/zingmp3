<?php

/*
* Parser and Getter for Mopidy
*/

if (!defined('BASE_PATH'))
    define('BASE_PATH', dirname(dirname(__FILE__)));

require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/lib/App.php';

$app = App::create();

$getter = new Pimusic\Getter();

$gmworker = new \GearmanWorker();

# Add default server (localhost).
$gmworker->addServer();

$gmworker->addFunction("getter", [$getter, 'work']);

print "Waiting for job...\n";
while ($gmworker->work()) {
    if ($gmworker->returnCode() != GEARMAN_SUCCESS) {
        echo "return_code: " . $gmworker->returnCode() . "\n";
        break;
    }
}
