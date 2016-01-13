<?php

namespace Pimusic\Parser;

class Zingmp3 extends ParserAbstract
{
    protected $PATTERNS = [
        'song'      => 'http:\/\/mp3\.zing\.vn\/bai\-hat\/.*\.html',
        'playlist'  => 'http:\/\/mp3\.zing\.vn\/playlist\/.*\.html',
        'album'     => 'http:\/\/mp3\.zing\.vn\/album\/.*\.html',
	    'radio'	    => 'http:\/\/radio\.zing\.vn\/.*'
    ];

    public function match($text)
    {
        $matches = null;
        foreach ($this->PATTERNS as $key => $pattern) {
            $result = preg_match("/$pattern/i", $text, $matches);
            if ($result) {
                return $matches[0];
            }
        }
        return FALSE;
    }

    public function fetch($url)
    {
        $matches = null;
        foreach ($this->PATTERNS as $key => $pattern) {
            $result = preg_match("/$pattern/i", $url, $matches);
            if ($result) {
                $url = $matches[0];
                switch ($key) {
                    case 'song':
                        return $this->fetchSong($url);
                    case 'album':
                    case 'playlist':
                        return $this->fetchPlaylist($url);
		    case 'radio':
			return $this->fetchRadio($url);	
		
                }
                return false;
            }
        }


    }

    public function fetchSong($url) {
        $foundItems = [];

        $downloader = \App::getDownloader();

        $page = $downloader->getCacheContent($url, [
            'prefix' => '/html',
            'suffix' => '.html',
            'gzip' => 1,
        ]);

        // Find XML link in HTML body
        $pattern = 'xmlURL=(http:\/\/mp3\.zing\.vn\/xml\/song\-xml\/[a-zA-Z0-9]+)';
        $matches = [];
        $result = preg_match("/$pattern/", $page, $matches);

        //var_dump($matches);

        if ($result) {

            $page = $downloader->getCacheContent(trim($matches[1]), [
                'prefix' => '/meta',
                'suffix' => '.xml',
                'gzip' => 1,
            ]);

            $foundItems = $this->getItem($page);

        }

        return $foundItems;
    }

    public function fetchPlaylist($url) {
        $foundItems = [];
        $downloader = \App::getDownloader();

        $page = $downloader->getContent($url);

        $document = \phpQuery::newDocument($page);

        $metaTitle = $document->find('meta[property="og:title"]');
        \App::dispatchEvent('playlist_fetch', Array(
            'title' => $metaTitle->attr("content"),
        ));


        $matches = $document->find('.item-song a.fn-name');
        foreach ($matches as $item) {

            $href = pq($item)->attr('href');

            $songItems = $this->fetchSong($href);
            if (count($songItems)>0) {
                $songItem = $songItems[0];
                $foundItems[] = $songItem;

                \App::dispatchEvent('playlist_fetch_song', Array(
                    'id' => count($foundItems),
                    'item' => $songItem,
                ));
            }
        }

        //\phpQuery::unloadDocuments();

        return $foundItems;

    }

    public function fetchRadio($url) {

        $foundItems = [];
        $downloader = \App::getDownloader();

        $page = $downloader->getCacheContent($url, [
            'prefix' => '/xml',
            'suffix' => '.xml',
            'gzip' => 1,
        ]);
        $document = \phpQuery::newDocument($page);
        $matches = $document->find('#fnRadioPlayer5Con');
        foreach($matches as $item) {

            $href = pq($item)->attr('xml');
            $page = $downloader->getCacheContent($href, [
                'prefix' => '/meta',
                'suffix' => '.xml',
                'gzip' => 1,
            ]);

            $foundItems = array_merge($foundItems,$this->getItem($page));
        }
        return $foundItems;
    }

    private function getItem ($page) {
        $foundItems = [];
        $downloader = \App::getDownloader();
        $pattern = '\<source\>\<\!\[CDATA\[(.*)\]\]\>\<\/source\>';
        preg_match("/$pattern/", $page, $matches);
        $url = trim($matches[1]);

        $pattern = '\<title\>\<\!\[CDATA\[(.*)\]\]\>\<\/title\>';
        preg_match("/$pattern/", $page, $matches);
        $title = trim($matches[1]);

        $pattern = '\<performer\>\<\!\[CDATA\[(.*)\]\]\>\<\/performer\>';
        preg_match("/$pattern/", $page, $matches);
        $artists = trim($matches[1]);

        if ($title != '' && $url != '') {
            echo "Found song: $title\n";
            echo "Downloading media $url\n\n";
            $path = $downloader->getCachePath($url, [
                'prefix' => '/media',
                'suffix' => '.mp3'
            ]);

            $foundItems[] = [
                'title' => $title,
                'artists' => $artists,
                'url'   => $url,
                'path'  => $path,
            ];

        }
        return $foundItems;
    }


}
