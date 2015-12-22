<?php

if (!defined('BASE_PATH'))
    define('BASE_PATH', dirname(dirname(__FILE__)));

require BASE_PATH . '/vendor/autoload.php';

$document = phpQuery::newDocumentFile('/tmp/pimusic/cache/html/http-mp3-zing-vn-playlist-hrismas-windy138-7-html.html');

$matches = $document->find('.item-song a.fn-name');
foreach ($matches as $item) {

    var_dump(pq($item)->attr('href'));
}
