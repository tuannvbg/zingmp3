<?php

class Zingmp3Test extends PHPUnit_Framework_TestCase
{
    public function testParseSong()
    {
        $parser = new Pimusic\Parser\Zingmp3();

        $url = '<http://mp3.zing.vn/bai-hat/Chuyen-Mua-Acoustic-Version-Trung-Quan-Idol/ZWZEI8C6.html>';
        $result = $parser->match($url);
        $this->assertNotEquals(FALSE, $result, "Matching failed");

        $this->assertEquals('http://mp3.zing.vn/bai-hat/Chuyen-Mua-Acoustic-Version-Trung-Quan-Idol/ZWZEI8C6.html',
            $result, "Removing prefix/suffix failed");

    }

}

