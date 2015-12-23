<?php

namespace Pimusic\Parser;

class Nhaccuatui extends ParserAbstract
{
    protected $PATTERNS = [
        'song'       => 'http:\/\/www\.nhaccuatui\.com\/bai\-hat\/.*\.html',
        'playlist'   => 'http:\/\/www\.nhaccuatui\.com\/playlist\/.*\.html',
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
        $pattern = 'xmlURL\s=\s"([^"]+)"';
        $matches = [];
        $result = preg_match("/$pattern/", $page, $matches);

        if ($result) {

            $page = $downloader->getCacheContent(trim($matches[1]), [
                'prefix' => '/meta',
                'suffix' => '.xml',
                'gzip' => 1,
            ]);

            $pattern = '<location>\s*<\!\[\CDATA\[(.*)\]\]>\s*<\/location>';
            preg_match("/$pattern/", $page, $matches);
            $url = trim($matches[1]);

            $pattern = '<title>\s*<\!\[\CDATA\[(.*)\]\]>\s*<\/title>';
            preg_match("/$pattern/", $page, $matches);
            $title = trim($matches[1]);

            $pattern = '<creator>\s*<\!\[\CDATA\[(.*)\]\]>\s*<\/creator>';
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

        $matches = $document->find('.item_content a.name_song');
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
