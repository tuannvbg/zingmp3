<?php

if (!defined('BASE_PATH'))
    define('BASE_PATH', dirname(__FILE__));

require_once 'lib/MopidyRemote.php';

define('BOT_PREFIX', 'slackpibot: ');


$params = $_REQUEST;
$text = isset($params['text']) ? $params['text'] : '';
$userName = isset($params['user_name']) ? $params['user_name'] : '';

$remote = new MopidyRemote();

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
            $parserList = ['Zingmp3', 'Nhaccuatui'];
            foreach ($parserList as $parserName) {
                require_once "lib/Parser/${parserName}.php";
                $className = "App\\Parser\\" . "$parserName";
                $parser = new $className;
                if ($parser->match($text)) {
                    $queue = new GearmanClient();
                    $queue->addServer(); //localhost
                    $job = $queue->doBackground('getter', json_encode($params));
                    /*
                                        $mediaData = $parser->getMedia($text);
                                        $remote->add($mediaData);

                                        $responseText = "@$userName: Đã nhận hàng";
                                        if ($mediaData['title'] != '') $responseText .= ' "'.$mediaData['title'].'" :ok_hand:';
                    */
                    break;
                } else {
                    // Just ignore unkown command
                }

            }
    }
}


if ($responseText != '') {
    echo json_encode(['text' => $responseText]);
}
