<?php

if (!defined('BASE_PATH'))
    define('BASE_PATH', dirname(dirname(__FILE__)));

require_once BASE_PATH.'/lib/Downloader.php';
require_once BASE_PATH.'/lib/MopidyRemote.php';

class Getter {

    protected $_downloader = null;
    protected $_parsers = [];
    protected $_mopidy = null;

    public function __construct() {
	$this->_downloader = new Downloader();

        $parserList = ['Zingmp3', 'Nhaccuatui'];
	$parsers = [];
            foreach ($parserList as $parserName) {
                require_once BASE_PATH."/lib/Parser/${parserName}.php";
                $className = "App\\Parser\\"."$parserName";
                $parser = new $className;
		$parsers[] = $parser;
	    }

	$this->_parsers = $parsers;

	$this->_mopidy = new MopidyRemote();
    }

    public function work($job) {
	echo "Ah! new Job\n";
	$dataJson = $job->workload();
	$data = json_decode($dataJson, 1);
	$text = $data['text'];
            foreach ($this->_parsers as $parser) {
                if ($parser->match($text)) {
                    $mediaData = $parser->getMedia($text);

		    $url = $mediaData['url'];
		    echo "Found url: $url \n";
		    $urlCache = $this->_downloader->getCacheUrl($url);
		    $mediaData['url'] = $urlCache;
var_dump($mediaData);
//		    echo "Adding to Mopidy\n";
                    //$this->_mopidy->add($mediaData);			
		}
	    }	
    }
}
