<?php

$url = '.http://mp3.zing.vn/bai-hat/Minh-Yeu-Tu-Bao-Gio--Em-La-Ba-Noi-Cua-Anh-OST--Miu-Le/ZW7WFEIW.html?a';

$norm = trim(preg_replace('/[^a-z0-9]+/', '-', $url), '-');
echo $norm.PHP_EOL;
