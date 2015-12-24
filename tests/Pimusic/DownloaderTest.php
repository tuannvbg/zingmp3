<?php

class DownloaderTest extends PHPUnit_Framework_TestCase
{
    public function testNormalize()
    {
        $downloader = App::getDownloader();
        $url = '.http://mp3.zing.vn/bai-hat/Minh-Yeu-Tu-Bao-Gio--Em-La-Ba-Noi-Cua-Anh-OST--Miu-Le/ZW7WFEIW.html?a';

        $method = self::getAccessibleMethod('\Pimusic\Downloader', '_normalize');

        $this->assertEquals('http-mp3-zing-vn-bai-hat-inh-eu-u-ao-io-m-a-a-oi-ua-nh-iu-e-7-html-a',
            $method->invoke($downloader, $url),
            "Normalize does not match");

    }

    protected static function getAccessibleMethod($class, $name) {
        $class = new ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }
}

