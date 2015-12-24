<?php

namespace Pimusic;


class Parser
{
    protected $_config = null;
    protected $_plugins = null;

    public function __construct($config)
    {
        $this->_config = $config;
        $pluginList = $this->_config['plugins'];

        $parsers = [];
        foreach ($pluginList as $parserName) {
            $className = "Pimusic\\Parser\\" . "$parserName";
            $parser = new $className;
            $parsers[] = $parser;
        }

        $this->_plugins = $parsers;


    }

    /**
     * Check if $text match any of Parser rules (Zingmp3, Nhaccuatui: song, playlist, etc)
     *
     * @param $text
     * @return bool
     */
    public function match($text) {
        $found = false;

        foreach ($this->_plugins as $parser) {
            $result = $parser->match($text);

            if ($result !== FALSE) {
                return $result;
            }
        }

        return $found;
    }

    /**
     * Download meta content and music file
     *
     * @param $text
     * @return bool
     */
    public function fetch($text) {
        $found = false;

        foreach ($this->_plugins as $parser) {

            $result = $parser->fetch($text);
            if ($result !== FALSE && $result !== NULL) {
                return $result;
            }
        }

        return $found;
    }



}