<?php

namespace Pimusic;

class Getter
{

    /**
     * @var \Pimusic\Downloader
     */
    protected $_downloader = null;

    /**
     * @var \Pimusic\Parser
     */
    protected $_parser = null;

    /**
     * @var \Pimusic\Mopidy
     */
    protected $_mopidy = null;

    public function __construct()
    {
        $this->_downloader = \App::getDownloader();

        $this->_mopidy = \App::getMopidy();

        $this->_parser = \App::getParser();

    }

    public function work($job)
    {
        echo "Ah! new Job\n";
        $dataJson = $job->workload();
        $data = json_decode($dataJson, 1);
        $url  = $data['url'];
        $media = $this->_parser->fetch($url);

        if ($media !== NULL) {
            var_dump($media);
        }
        else {
            echo "Not match!\n";
        }

//var_dump($data);

//        $this->_parser->parse($text, function($mediaData) use ($this) {
//            $url = $mediaData['url'];
//            echo "Found url: $url \n";
//            $urlCache = $this->_downloader->getCacheUrl($url);
//            $mediaData['url'] = $urlCache;
//            var_dump($mediaData);
////		    echo "Adding to Mopidy\n";
//            //$this->_mopidy->add($mediaData);
//
//        });

    }
}
