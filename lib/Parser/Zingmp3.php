<?php

namespace App\Parser;

require_once 'ParserAbstract.php';

class Zingmp3 extends ParserAbstract
{

    public function match($text)
    {
        $result = strpos($text, 'http://mp3.zing.vn/bai-hat/');
        return $result !== FALSE;
    }

    public function getMedia($url)
    {

        $url = preg_replace('/.*(http:\/\/mp3\.zing\.vn\/bai\-hat\/.*\.html).*/i', "$1", $url);
        $page = $this->getLink($url, $gzip=true);

        $pattern = 'xmlURL=(http:\/\/mp3\.zing\.vn\/xml\/song\-xml\/[a-zA-Z0-9]+)';
        $matches = [];
        $result = preg_match("/$pattern/", $page, $matches);

        //var_dump($matches);

        if ($result) {

            $page = $this->getLink($matches[1], $gzip=true);

            $pattern = '\<source\>\<\!\[CDATA\[(.*)\]\]\>\<\/source\>';
            preg_match("/$pattern/", $page, $matches);
            $url = $matches[1];

            $pattern = '\<title\>\<\!\[CDATA\[(.*)\]\]\>\<\/title\>';
            preg_match("/$pattern/", $page, $matches);
            $title = $matches[1];

            return [
                'title' => trim($title),
                'url' => trim($url)
            ];
        } else {
            return FALSE;
        }

    }


}
