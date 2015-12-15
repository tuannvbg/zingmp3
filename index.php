<?php
require_once 'ZingParser.php';
require_once 'MopidyRemote.php';

define('BOT_PREFIX', 'slackpibot: ');

function echoSlackText($text)
{
    $text = $text;
    echo json_encode(['text' => $text]);
}

$params = $_REQUEST;
$text = isset($params['text']) ? $params['text'] : '';

$remote = new MopidyRemote();

$parser = new ZingParser();

if (strpos($text, BOT_PREFIX) === 0) {
    // Ignore
} else if ($text == 'next') {
    $remote->next();
    echoSlackText('Neeeeeeeeeext!');
} else if ($text == 'play') {
    $remote->play();
    echoSlackText('Ok!');
} else if ($text == 'stop') {
    $remote->stop();
    echoSlackText('Shhh!');
} else if ($text == 'clear') {
    $remote->clear();
    echoSlackText('Empty!');
} else if ($text !== '') {
    if ($parser->match($text)) {
        $mediaData = $parser->getMedia($text);
        $remote->add($mediaData);
        echoSlackText("Received");
    } else {
        //                echoSlackText('Not Match');
    }
} else {
    echo BOT_PREFIX . 'Invalid parameters';
}


