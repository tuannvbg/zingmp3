<?php

class NhaccuatuiTest extends PHPUnit_Framework_TestCase
{
    public function testParseSong()
    {
        $parser = new Pimusic\Parser\Nhaccuatui();

        $url = '<http://www.nhaccuatui.com/bai-hat/mot-nha-da-lab.lCr2JWr7FUFv.html>';
        $result = $parser->match($url);
        $this->assertNotEquals(FALSE, $result, "Matching failed");

        $this->assertEquals('http://www.nhaccuatui.com/bai-hat/mot-nha-da-lab.lCr2JWr7FUFv.html',
            $result, "Removing prefix/suffix failed");

    }

}

