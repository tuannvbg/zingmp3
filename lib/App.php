<?php


class App
{

    static protected $_config = null;
    static protected $_instance = null;

    /**
     * @return App
     */
    static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new static();
        }

        return self::$_instance;
    }

    static function getConfig($key=null) {
        if ($key != null)
            return self::$_config['key'];
        else
            return self::$_config;
    }

    static function create($config) {
        self::$_config = $config;

        self::$_instance = self::getInstance();

        return self::$_instance;
    }

    static function log($content, $file = 'app.log') {
        $logPath = self::$_config['log_path'];
        if (!file_exists($logPath)) {
            mkdir($logPath, 0777, $recursive=true);
        }

        $logFile = "$logPath/$file";
        $h = fopen($logFile, 'w+');
        fwrite($h, $logFile, $content.PHP_EOL);
        fclose($h);
    }

    /**
     * @return \Pimusic\Downloader
     */
    static function getDownloader() {
        $config = self::$_config;
        return new \Pimusic\Downloader(['cache_path' => $config['cache_path']]);
    }

    /**
     * @return \Pimusic\MopidyRemote
     */
    static function getMopidy() {
        $config = self::$_config;
        return new \Pimusic\MopidyRemote( $config['mopidy']);
    }

    /**
     * @return \Pimusic\Parser
     */
    static function getParser() {

        $config = self::$_config;
        return new \Pimusic\Parser($config['parser']);
    }

    /**
     * @return \Pimusic\Queue
     */
    static function getQueue() {

        $config = self::$_config;
        return new \Pimusic\Queue($config['queue']);
    }

}