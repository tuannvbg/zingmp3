<?php

require_once 'lib/Parser/Nhaccuatui.php';

$parser = new App\Parser\Nhaccuatui();

$url = 'http://www.nhaccuatui.com/bai-hat/mot-nha-da-lab.lCr2JWr7FUFv.html';
var_dump($parser->match($url));

$data = $parser->getMedia($url);
var_dump($data);