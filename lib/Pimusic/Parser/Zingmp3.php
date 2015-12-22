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
                        return $this->fetchAlbum($url);
                    case 'playlist':
                        return $this->fetchPlaylist($url);
                }
                return false;
            }
        }

        $url = preg_replace('/.*(http:\/\/mp3\.zing\.vn\/bai\-hat\/.*\.html).*/i', "$1", $url);


    }

    public function fetchSong($url) {
        $downloader = \App::getDownloader();

        $page = $downloader->getCacheContent($url, [
            'prefix' => '/html',
            'suffix' => '.html',
            'gzip' => 1,
        ]);

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

            $path = $downloader->getCachePath($url, [
                'prefix' => '/media',
                'suffix' => '.mp3'
            ]);

            return [
                'title' => $title,
                'artists' => $artists,
                'url'   => $url,
                'path'  => $path,
            ];
        } else {
            return FALSE;
        }
    }

    public function fetchPlaylist($url) {

    }

    public function fetchAlbum($url) {

    }

}
