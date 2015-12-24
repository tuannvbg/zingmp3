<?php

class ParserTest extends PHPUnit_Framework_TestCase
{
    public function testParseSong()
    {
        $parser = App::getParser();

        $url = '<http://mp3.zing.vn/bai-hat/Chuyen-Mua-Acoustic-Version-Trung-Quan-Idol/ZWZEI8C6.html>';
        $result = $parser->match($url);
        $this->assertNotEquals(FALSE, $result, "Matching failed");

        $this->assertEquals('http://mp3.zing.vn/bai-hat/Chuyen-Mua-Acoustic-Version-Trung-Quan-Idol/ZWZEI8C6.html',
            $result, "Removing prefix/suffix failed");

    }

}

