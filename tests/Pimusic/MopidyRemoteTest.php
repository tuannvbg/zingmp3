<?php

class MopidyRemoteTest extends PHPUnit_Framework_TestCase
{
    public function testConnection()
    {
        $mopidy = App::getMopidy();

        $method = self::getAccessibleMethod(get_class($mopidy), '_createRequest');
        $request = $method->invoke($mopidy, 'core.get_version');

        $method = self::getAccessibleMethod(get_class($mopidy), '_exec');
        $response = $method->invoke($mopidy, $request);

        $this->assertNotEquals(FALSE, $response, "Cannot connect to Mopidy");

    }

    public function testAddLocalFile() {
//        $data = ['url' => 'file:///var/lib/mopidy/mp3/minh-yeu-tu-bao-gio.mp3'];
//        $o = new MopidyRemote();
//        $o->add($data);
//        $o->play();

    }


    protected static function getAccessibleMethod($class, $name) {
        $class = new ReflectionClass($class);
        $method = $class->getMethod($name);
        $method->setAccessible(true);
        return $method;
    }

}

