<?php
class AppTest extends PHPUnit_Framework_TestCase
{

    public function testConfigReady()
    {
        $value = App::getConfig('app_name', '');

        $this->assertEquals('pimusic', strtolower($value), "Config is not loaded (default key app_name=pimusic not found)");

        // Test when a config value is not existed
        $value = App::getConfig('random_config_'.rand(0,999999), -1);
        $this->assertEquals(-1, $value, "Default value for config item is invalid");

    }

    public function testLog() {
        $logPath = App::getConfig('log_path', null);
        $this->assertNotEquals(null, $logPath, "log_path is not defined in config");

        $logFileName = '_test.log_';
        $logFile = $logPath.DIRECTORY_SEPARATOR.$logFileName;
        if (file_exists($logFile))
            unlink($logFile);

        App::log('hello', $logFileName);

        $this->assertFileExists($logFile, "Cannot create log file: $logFile");
        unlink($logFile);
    }

    public function testEvent() {

        App::registerEvent('testEvent', Array($this, 'sampleEventHandler'));

        App::dispatchEvent('testEvent', Array('var1' => 1, 'var2' => 2));


    }

    public function sampleEventHandler($params) {
        $this->assertEquals(1, $params['var1'], 'Param not found');
    }


}