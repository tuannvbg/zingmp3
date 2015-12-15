<?php

class ZingParser {

        public function match($text) {
                $result = strpos($text, 'http://mp3.zing.vn/bai-hat/');

                return $result !== FALSE;
        }

        public function getMedia($url) {

		$url = preg_replace('/.*(http:\/\/mp3\.zing\.vn\/bai\-hat\/.*\.html).*/i', "$1", $url);
                $page = $this->getGzipLink($url);

                $pattern = 'xmlURL=(http:\/\/mp3\.zing\.vn\/xml\/song\-xml\/[a-zA-Z0-9]+)';
                $matches = [];
                $result = preg_match("/$pattern/", $page, $matches);

                //var_dump($matches);

		if ($result) {

                $page = $this->getGzipLink($matches[1]);

                $pattern = '\<source\>\<\!\[CDATA\[(.*)\]\]\>\<\/source\>';
                preg_match("/$pattern/", $page, $matches);
                $url = return $matches[1];

                $pattern = '\<title\>\<\!\[CDATA\[(.*)\]\]\>\<\/title\>';
                preg_match("/$pattern/", $page, $matches);
                $title = return $matches[1];

		return [
			'title' => $title,
			'url' => $url
		];
		}
		else {
			return FALSE;
		}

        }

        public function getGzipLink($url) {

          $ch = curl_init();
          curl_setopt($ch, CURLOPT_URL, $url);
          curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
          curl_setopt($ch,CURLOPT_ENCODING , "gzip");
          curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
          $output = curl_exec($ch);
          curl_close($ch);

          return $output;

        }

}
