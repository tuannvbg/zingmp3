<?php


class App
{

    /**
     * @var Array
     */
    static protected $_config = null;

    /**
     * @var App
     */
    static protected $_instance = null;

    /**
     * Singleton implementation
     *
     * @return App
     */
    static function getInstance() {
        if (self::$_instance == null) {
            self::$_instance = new static();
        }

        return self::$_instance;
    }

    /**
     * Load config and local config
     */
    static function initConfig() {
        $configPath = BASE_PATH.'/config/main.php';
        $config = include $configPath;

        // Load local config if only it existed
        $localConfigPath = BASE_PATH.'/config/local.php';
        if (file_exists($localConfigPath)) {
            $localConfig = include $localConfigPath;
            $config = array_replace_recursive($config, $localConfig);
        }

        self::$_config = $config;

    }

    /**
     * Get all config or only entry via its key
     *
     * @param null $key
     * @return Array
     */
    static function getConfig($key=null) {
        if ($key != null)
            return self::$_config['key'];
        else
            return self::$_config;
    }

    /**
     * Main entry point
     *
     * @return App
     */
    static function create() {
        self::$_instance = self::getInstance();

        self::initConfig();

        return self::$_instance;
    }

    /**
     * Log content to a file in 'log_path' config
     *
     * @param $content
     * @param string $file
     */
    static function log($content, $file = 'app.log') {
        $logPath = self::$_config['log_path'];
        if (!file_exists($logPath)) {
            mkdir($logPath, 0777, $recursive=true);
        }

        $logFile = "$logPath/$file";
        $h = fopen($logFile, 'a+');
        fwrite($h, $content.PHP_EOL);
        fclose($h);
    }

    /**
     * Get new Downloader service
     *
     * @return \Pimusic\Downloader
     */
    static function getDownloader() {
        $config = self::$_config;
        return new \Pimusic\Downloader(['cache_path' => $config['cache_path']]);
    }

    /**
     * Get new MopidyRemote service
     *
     * @return \Pimusic\MopidyRemote
     */
    static function getMopidy() {
        $config = self::$_config;
        return new \Pimusic\MopidyRemote( $config['mopidy']);
    }

    /**
     * Get new Parser service
     *
     * @return \Pimusic\Parser
     */
    static function getParser() {

        $config = self::$_config;
        return new \Pimusic\Parser($config['parser']);
    }

    /**
     * Get new Queue service
     *
     * @return \Pimusic\Queue
     */
    static function getQueue() {

        $config = self::$_config;
        return new \Pimusic\Queue($config['queue']);
    }

    /**
     * Get new Slack service
     *
     * @return \Pimusic\Slack
     */
    static function getSlack() {

        $config = self::$_config;
        return new \Pimusic\Slack($config['slack']);
    }

}