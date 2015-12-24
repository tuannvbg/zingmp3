<?php

if (!defined('BASE_PATH'))
    define('BASE_PATH', dirname(__FILE__));

define('BOT_PREFIX', 'PiMusic');

require BASE_PATH . '/vendor/autoload.php';
require BASE_PATH . '/lib/App.php';


$app = App::create();

$params = $_REQUEST;
$text = isset($params['text']) ? $params['text'] : '';
$userName = isset($params['user_name']) ? $params['user_name'] : '';

$remote = App::getMopidy();

$responseText = '';


if ($text != '' && strpos($text, BOT_PREFIX) !== 0) {
    switch ($text) {
        case 'help':
        case '?':
            $responseText = "" .
                "- Info: `help`, `?`, `now`, `current`, `list`\n" .
                "- Playback: `next`, `play`, `stop`, `pause`, `resume`\n" .
                "- Supported links: ZingMP3, Nhaccuatui";
            break;
        case 'next':
            $remote->next();
            $responseText = 'Neeeeeeeeeext';
            break;
        case 'play':
            $remote->play();
            $responseText = 'Ok!';
            break;
        case 'pause':
            $remote->pause();
            $responseText = 'Ok!';
            break;
        case 'resume':
            $remote->resume();
            $responseText = 'Ok!';
            break;
        case 'stop':
            $remote->stop();
            $responseText = 'Shhh!';
            break;
        case 'clear':
            $remote->clear();
            $responseText = 'Clean & Clear!';
            break;
        case 'current':
        case 'now':
            $responseText = 'Current track: ' . $remote->getCurrent();
            break;
        case 'list':
            $responseText = $remote->listTracks();
            break;
        default:
            $result = \App::getParser()->match($text);

            if ($result !== FALSE) {
                \App::getQueue()->add('getter', ['url' => $result, 'originData' => $params]);
            }
    }
}


if ($responseText != '') {
    echo json_encode(['text' => $responseText]);
}
