<?php

class Downloader {

    const CACHE_PATH = '/pidata/cache';

    protected $_redis = null;
    protected $_curl = null;

    public function __construct() {
	$this->_redis = new Redis(); // localhost
	$this->_redis->connect('127.0.0.1', 6379);

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
	curl_setopt($ch, CURLOPT_ENCODING, "gzip");
	$this->_curl = $ch;
    }

    public function __destruct() {
	curl_close($this->_curl);
	$this->_redis->close();
    }

    public function getCache($url, $prefix='', $suffix='', $gzip=false) {

	$path = self::CACHE_PATH.$prefix;
	if (!file_exists($path)) {
		mkdir($path,  0777, true);
	}

	$path .= '/'.$this->_normalize($url).$suffix;

	if (file_exists($path) && filesize($path)>0) 
		$content = file_get_contents($path);
	else { 
	        curl_setopt($this->_curl, CURLOPT_URL, $url);
//        	if ($gzip) {
//            	    curl_setopt($this->_curl, CURLOPT_ENCODING, "gzip");
//        	}
		$content = curl_exec($this->_curl);

		// save cache
		file_put_contents($path, $content);
	}

	return $content;
    }

    protected function _normalize($url) {
	return trim(preg_replace('/[^a-z0-9]+/', '-', $url), '-');
    }

}
