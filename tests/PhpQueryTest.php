<?php
class PhpQueryTest extends PHPUnit_Framework_TestCase
{
    public function testFind()
    {
        $htmlPath = BASE_PATH.'/samples/zingmp3_playlist.html';
        $document = phpQuery::newDocumentFile($htmlPath);

        $matches = $document->find('.item-song a.fn-name');
        $item = pq($matches[0]);

        $this->assertEquals('http://mp3.zing.vn/bai-hat/The-First-Noel-David-Archuleta/IW6UUBOW.html',
            $item->attr('href'), "Attribute and Value are not found");
    }
}