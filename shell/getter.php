<?php

/*
* Parser and Getter for Mopidy
*/

if (!defined('BASE_PATH'))
    define('BASE_PATH', dirname(dirname(__FILE__)));

require_once BASE_PATH.'/lib/Getter.php';

$getter = new Getter();

$gmworker= new GearmanWorker();

# Add default server (localhost).
$gmworker->addServer();

$gmworker->addFunction("getter", [$getter, 'work']);

print "Waiting for job...\n";
while($gmworker->work())
{
  if ($gmworker->returnCode() != GEARMAN_SUCCESS)
  {
    echo "return_code: " . $gmworker->returnCode() . "\n";
    break;
  }
}
