<?php

namespace Pimusic\Parser;

class Zingmp3 extends ParserAbstract
{
    const PATTERNS = [
        'song'      => 'http:\/\/mp3\.zing\.vn\/bai\-hat\/.*\.html',
        'playlist'  => 'http:\/\/mp3\.zing\.vn\/playlist\/.*\.html',
        'album'     => 'http:\/\/mp3\.zing\.vn\/album\/.*\.html',
    ];

    public function match($text)
    {
        $matches = null;
        foreach (self::PATTERNS as $key => $pattern) {
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
        foreach (self::PATTERNS as $key => $pattern) {
            $result = preg_match("/$pattern/i", $url, $matches);
            if ($result) {
                $url = $matches[0];
                switch ($key) {
                    case 'song':
                        return $this->fetchSong($url);
                    case 'album':
                    case 'playlist':
                        return $this->fetchPlaylist($url);
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

        }

        return $foundItems;
    }

    public function fetchPlaylist($url) {
        $foundItems = [];
        $downloader = \App::getDownloader();

        $page = $downloader->getCacheContent($url, [
            'prefix' => '/html',
            'suffix' => '.html',
            'gzip' => 1,
        ]);

        $document = \phpQuery::newDocument($page);

        $matches = $document->find('.item-song a.fn-name');
        foreach ($matches as $item) {

            $href = pq($item)->attr('href');

            $songItems = $this->fetchSong($href);
            if (count($songItems)>0)
                $foundItems[] = $songItems[0];
        }

        //\phpQuery::unloadDocuments();

        return $foundItems;

    }


}
