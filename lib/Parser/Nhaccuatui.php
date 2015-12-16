<?php

namespace App\Parser;

require_once 'ParserAbstract.php';

class Nhaccuatui extends ParserAbstract
{

    public function match($text)
    {
        $result = strpos($text, 'http://www.nhaccuatui.com/bai-hat/');

        return $result !== FALSE;
    }

    public function getMedia($url)
    {

        $url = preg_replace('/.*(http:\/\/www\.nhaccuatui\.com\/bai\-hat\/.*\.html).*/i', "$1", $url);
        $page = $this->getLink($url, $gzip=true);

        $pattern = 'xmlURL\s=\s"([^"]+)"';
        $matches = [];
        $result = preg_match("/$pattern/", $page, $matches);

        //var_dump($matches);

        if ($result) {

            $page = $this->getLink($matches[1], $gzip=true);

            $pattern = '<location>\s*<\!\[\CDATA\[(.*)\]\]>\s*<\/location>';
            preg_match("/$pattern/", $page, $matches);

            $url = $matches[1];

            $pattern = '<title>\s*<\!\[\CDATA\[(.*)\]\]>\s*<\/title>';
            preg_match("/$pattern/", $page, $matches);
            $title = $matches[1];

            return [
                'title' => $title,
                'url' => $url
            ];
        } else {
            return FALSE;
        }

    }


}
