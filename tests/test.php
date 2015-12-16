<?php

$s = file_get_contents('tl.txt');

$o = json_decode($s, 1);

$list = $o['result'];

$data = [];
foreach ($list as $item) {
	$track = $item['track'];
	$new['name'] = isset($track['name'])?$track['name']:'Unknown';
	$new['tlid'] = $item['tlid'];
	$data[] = $new;
}

print_r($data);
