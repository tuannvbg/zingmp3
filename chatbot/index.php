<?php

$params = $_REQUEST;
$text = isset($params['text']) ? $params['text'] : '';
$userName = isset($params['user_name']) ? $params['user_name'] : '';

file_put_contents('/tmp/chat.log', json_encode($params));

if ($userName == 'slackbot') die;
if ($text == '') die;

require_once('simsimi_api_class.php');

$obj = new SimSimiAPI();
$responseText = '';
try{
	$result = $obj->simSimiConversation('vn', '1.0', $text);
	$responseText = $result['response'];

}catch(Exception $e){
	file_put_contents('/tmp/chat_error.log', $e->getMessage());
}


if ($responseText !== '') {
//	file_put_contents('/tmp/chat_debug.log', $responseText);
	echo json_encode(['text' => $responseText]);
}
