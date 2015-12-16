<?php

namespace App\Parser;

abstract class ParserAbstract
{

    abstract public function match($text);

    abstract public function getMedia($url);

    public function getLink($url, $gzip=true)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        if ($gzip) {
            curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        }
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
        $output = curl_exec($ch);
        curl_close($ch);

        return $output;

    }

}
