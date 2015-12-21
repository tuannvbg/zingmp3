<?php

namespace App\Parser;

require_once BASE_PATH.'/lib/Downloader.php';

abstract class ParserAbstract
{

    protected $_downloader = null;

    abstract public function match($text);

    abstract public function getMedia($url);


    public function __construct() {
	$this->_downloader = new \Downloader();
    }

    public function getLink($url, $gzip=true)
    {
	return $this->_downloader->getCache($url, '/meta');

    }

}
