<?php

class MopidyRemote
{

    protected $_curl;

    public function __construct()
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, 'http://125.234.98.126:6680/mopidy/rpc');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");

        $this->_curl = $ch;

    }

    public function __destruct()
    {
        curl_close($this->_curl);
    }

    protected function _createParam()
    {
        $obj = [
            'jsonrpc' => "2.0",
            'params' => [],
            'id' => 1
        ];
        return $obj;
    }

    protected function _exec($params)
    {
        $paramsStr = json_encode($params);

        curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $paramsStr);
        curl_setopt($this->_curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($paramsStr)));

        $result = curl_exec($this->_curl);
        file_put_contents('/tmp/slackpi', $result);
        return $result;
    }

    public function stop()
    {
        $params = $this->_createParam();
        $params['method'] = 'core.playback.stop';
        $this->_exec($params);

    }

    public function add($data)
    {

        $params = $this->_createParam();
        $params['method'] = 'core.tracklist.add';
        $params['params'] = [
            'uri' => $data['url']
        ];

    }

    public function next()
    {
        $params = $this->_createParam();
        $params['method'] = 'core . playback . next';
        $this->_exec($params);
    }

    public function play()
    {
        $params = $this->_createParam();
        $params['method'] = 'core . playback . play';
        $params['params'] = [
        ];
        $this->_exec($params);

    }

    public function clear()
    {
        $params = $this->_createParam();
        $params['method'] = 'core . tracklist . clear';
        $this->_exec($params);
    }

}

