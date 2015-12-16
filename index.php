<?php
require_once 'lib/ZingParser.php';
require_once 'lib/MopidyRemote.php';

define('BOT_PREFIX', 'slackpibot: ');


$params = $_REQUEST;
$text = isset($params['text']) ? $params['text'] : '';

$remote = new MopidyRemote();

$parser = new ZingParser();

$responseText = '';

if ($text != '' && strpos($text, BOT_PREFIX) !== 0) {
    switch ($text) {
        case 'help':
        case '?':
            $responseText = 'help, ?, next, play, stop, list, (link Zing MP3)';
            break;
        case 'next':
            $remote->next();
            $responseText = 'Neeeeeeeeeext';
            break;
        case 'play':
            $remote->play();
            $responseText = 'Ok!';
            break;
        case 'stop':
            $remote->stop();
            $responseText = 'Shhh!';
            break;
        case 'clear':
            $remote->clear();
            $responseText = 'Empty!';
            break;
        case 'list':
            $responseText = $remote->listTracks();
            break;
        default:
            if ($parser->match($text)) {
                $mediaData = $parser->getMedia($text);
                $remote->add($mediaData);

                $responseText = "Received";
                if ($mediaData['title'] != '') $responseText .= ': '.$mediaData['title'];
            } else {
                // Just ignore unkown command
            }
    }
} 


if ($responseText != '') {
    echo json_encode(['text' => $responseText]);
}