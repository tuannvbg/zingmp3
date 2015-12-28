<?php

require_once 'autoload.php';

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
                "- Info: `help`, `?`, `status`=`ping`=`hey`, `wake up`, `sleep`\n".
                "- Tracklist: `now`=`current`, `list`\n" .
                "- Playback: `next`, `play`, `stop`, `pause`, `resume`\n" .
                "- Supported links (song/album/playlist): ZingMP3, Nhaccuatui";
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
        case 'ping':
        case 'hey':
        case 'status':
            $isRunning = $remote->getServiceStatus();
            $responseText = ($isRunning)?"Vẫn sống nhăn :grin:":":zzz:";
            break;
        case 'wakeup':
        case 'wake up':
        case 'wake_up':
        case 'wake-up':
            $isRunning = $remote->getServiceStatus();
            if ($isRunning)
                $responseText = "Vẫn tỉnh nãy giờ :unamused:";
            else {
                $remote->startService();
                $isRunning = $remote->getServiceStatus();
                $responseText = ($isRunning)?"Hế nhô :kissing_smiling_eyes:":":zzz:";
            }
            break;
        case 'sleep':
            $remote->stopService();
            $isRunning = $remote->getServiceStatus();
            $responseText = ($isRunning)?"Ko hiểu sao nhưng vưỡn tỉnh như sáo :flushed:":":zzz:";
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
