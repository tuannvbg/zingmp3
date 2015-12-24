<?php

class SlackTest extends PHPUnit_Framework_TestCase
{
    public function testSendMessage()
    {
        $slack = App::getSlack();

        $response = $slack->send("1,2,3 unit testing");

        $this->assertEquals('ok', $response, "No reply from Slack");

    }


}

