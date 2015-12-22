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

    /**
     * @var \Pimusic\Slack
     */
    protected $_slack = null;

    public function __construct()
    {
        $this->_downloader = \App::getDownloader();

        $this->_mopidy = \App::getMopidy();

        $this->_parser = \App::getParser();

        $this->_slack = \App::getSlack();

    }

    public function work($job)
    {
        echo "Ah! new Job\n";
        $dataJson = $job->workload();
        $data = json_decode($dataJson, 1);
        $url  = $data['url'];

        $this->_slack->setRequestData($data['originData']);

        $foundItems = $this->_parser->fetch($url);

        $uris = [];

        if (count($foundItems) > 0) {
            $count = count($foundItems);
            echo "Found $count items\n";
            $i = 0;
            foreach ($foundItems as $item) {
                $i++;
                echo "$i. ".$item['title']."\n";

//                $this->_mopidy->add($uris);
                $uris[] = "file://".$item['path'];
                //$uris[] = $item['url'];
            }

            print_r($foundItems[0]);

            $this->_slack->notifySongsAdded($foundItems);
            $this->_mopidy->add($uris);
        }
        else {
            echo "Not found any!\n";
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
