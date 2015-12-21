<?php
require_once '../lib/MopidyRemote.php';

$data = ['url' => 'file:///var/lib/mopidy/mp3/minh-yeu-tu-bao-gio.mp3'];
$o = new MopidyRemote();
$o->add($data);
$o->play();
