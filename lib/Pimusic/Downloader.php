<?php

namespace Pimusic;

class Downloader
{

//    protected $_redis = null;
    protected $_config = null;
    protected $_curl = null;

    public function __construct($config)
    {
//        $this->_redis = new Redis(); // localhost
//        $this->_redis->connect('127.0.0.1', 6379);
        $this->_config = $config;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
        curl_setopt($ch, CURLOPT_ENCODING, "gzip");
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $this->_curl = $ch;
    }

    public function __destruct()
    {
        curl_close($this->_curl);
//        $this->_redis->close();
    }

    public function getCache($url, $options = [])
    {
        $cachePath = $this->_config['cache_path'];

        $prefix = isset($options['prefix'])?$options['prefix']:'';
        $suffix = isset($options['suffix'])?$options['suffix']:'';

        $path = $cachePath . $prefix;
        if (!file_exists($path)) {
            mkdir($path, 0777, true);
        }

        $path .= '/' . $this->_normalize($url) . $suffix;

//        echo "Downloading $url\n";

        if (file_exists($path) && filesize($path) > 0)
            $content = file_get_contents($path);
        else {
//            echo "CACHE MISS!\n";
            curl_setopt($this->_curl, CURLOPT_URL, $url);
            $content = curl_exec($this->_curl);

            // save cache
            file_put_contents($path, $content);
        }

        return [
            'content' => $content,
            'path' => $path,
            ];
    }

    public function getCachePath($url, $options = [])
    {
        $result = $this->getCache($url, $options);
        return $result['path'];
    }

    public function getCacheContent($url, $options = [])
    {
        $result = $this->getCache($url, $options);
        return $result['content'];
    }

    protected function _normalize($url)
    {
        return trim(preg_replace('/[^a-z0-9]+/', '-', $url), '-');
    }

}
