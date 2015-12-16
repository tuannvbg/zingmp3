<?php

require_once 'lib/Parser/Zingmp3.php';

$parser = new App\Parser\Zingmp3();

$url = 'http://mp3.zing.vn/bai-hat/Chuyen-Mua-Acoustic-Version-Trung-Quan-Idol/ZWZEI8C6.html';
var_dump($parser->match($url));

$data = $parser->getMedia($url);
var_dump($data);