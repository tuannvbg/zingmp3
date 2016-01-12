<?php


class App
{

    /**
     * @var App
     */
    static protected $_instance = null;

    /**
     * @var array
     */
    protected $_config = Array();

    /**
     * @var array
     */
    protected $_eventChain = Array();

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
     * Main entry point
     *
     * @return App
     */
    static function create() {
        self::$_instance = self::getInstance();

        self::$_instance->initConfig();

        return self::$_instance;
    }


    protected function __construct()
    {

    }


    /**
     * Load config and local config
     */
    public function initConfig() {
        $configPath = BASE_PATH.'/config/main.php';
        $config = include $configPath;

        // Load local config if only it existed
        $localConfigPath = BASE_PATH.'/config/local.php';
        if (file_exists($localConfigPath)) {
            $localConfig = include $localConfigPath;
            $config = array_replace_recursive($config, $localConfig);
        }

        $this->_config = $config;

    }

    /**
     * Get all config or only entry via its key
     *
     * @param null $key
     * @param null $defaultValue
     * @return array
     */
    static function getConfig($key=null, $defaultValue=null) {
        $instance = self::getInstance();
        if ($key != null)
            return isset($instance->_config[$key])?$instance->_config[$key]:$defaultValue;
        else
            return $instance->_config;
    }

    /**
     * Log content to a file in 'log_path' config
     *
     * @param $content
     * @param string $file
     */
    static function log($content, $file = 'app.log') {
        $logPath = self::getConfig('log_path');

        if (!file_exists($logPath)) {
            mkdir($logPath, 0777, $recursive=true);
        }

        $logFile = "$logPath/$file";
        $h = fopen($logFile, 'a+');
        if ($h) {
              fwrite($h, $content.PHP_EOL);
              fclose($h);
        }
    }

    /**
     * Get new Downloader service
     *
     * @return \Pimusic\Downloader
     */
    static function getDownloader() {
        return new \Pimusic\Downloader(['cache_path' => self::getConfig('cache_path')]);
    }

    /**
     * Get new MopidyRemote service
     *
     * @return \Pimusic\MopidyRemote
     */
    static function getMopidy() {
        return new \Pimusic\MopidyRemote(self::getConfig('mopidy'));
    }

    /**
     * Get new Parser service
     *
     * @return \Pimusic\Parser
     */
    static function getParser() {
        return new \Pimusic\Parser(self::getConfig('parser'));
    }

    /**
     * Get new Queue service
     *
     * @return \Pimusic\Queue
     */
    static function getQueue() {
        return new \Pimusic\Queue(self::getConfig('queue'));
    }

    /**
     * Get new Slack service
     *
     * @return \Pimusic\Slack
     */
    static function getSlack() {
        return new \Pimusic\Slack(self::getConfig('slack'));
    }

    /**
     * Raise an event, also sending its parameters
     *
     * @param $eventName
     * @param $params
     */
    static function dispatchEvent($eventName, $params) {
        $instance = self::getInstance();
        $eventChains = $instance->_eventChain;

        if (isset($eventChains[$eventName])) {
            foreach ($eventChains[$eventName] as $handler) {
                $result = $handler($params);

                // option to break chain
                if ($result === FALSE) break;
            }
        }

    }

    /**
     * Register event handler, via a callback
     *
     * @param $eventName
     * @param $callback
     */
    static function registerEvent($eventName, $callback) {

        $instance = self::getInstance();
        $eventChains = $instance->_eventChain;

        if (!isset($eventChains[$eventName]))
            $eventChains[$eventName] = [];

        $eventChains[$eventName][] = $callback;

        $instance->_eventChain = $eventChains;
    }

}
