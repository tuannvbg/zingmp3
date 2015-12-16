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

    public function stop()
    {
        $this->_exec($this->_createRequest('core.playback.stop'));
    }

    public function next()
    {
        $this->_exec($this->_createRequest('core.playback.next'));
    }

    public function play()
    {
        $this->_exec($this->_createRequest('core.playback.play'));
    }

    public function resume()
    {
        $this->_exec($this->_createRequest('core.playback.resume'));
    }

    public function pause()
    {
        $this->_exec($this->_createRequest('core.playback.pause'));
    }

    public function clear()
    {
        $this->_exec($this->_createRequest('core.tracklist.clear'));
    }

    public function add($data)
    {
        $request = $this->_createRequest('core.tracklist.add');
        $request['params'] = [
            'uri' => $data['url']
        ];
        $this->_exec($request);
    }

    public function listTracks() {
        $request = $this->_createRequest('core.tracklist.get_tracks');
        $response = $this->_exec($request);

        $responseData = json_decode($response, 1);

        $responseText = "";
        $data = [];
        foreach ($responseData['result'] as $item) {
            $name = $this->_getTrackName($item);
            $data[] = $name;
        }

        return implode("\n", $data);
    }

    public function getCurrent() {
        $request = $this->_createRequest('core.playback.get_current_tl_track');
        $response = $this->_exec($request);

        $responseData = json_decode($response, 1);
        $name = $this->_getTrackName($responseData['result']['track']);

        return $name;
    }
    /**
    * 
    */
    protected function _getTrackName($track) {
        $name = isset($track['name'])?$track['name']:'Unknown';
        // Add artists info to name
        if (isset($track['artists'])) {
            $artists = [];
            foreach ($track['artists'] as $a) {
                $artists[] = $a['name'];
            }

            if (count($artists)>0) {
                $artistInfo = implode(', ', $artists);
                $name .= $artistInfo;
            }
            
        }

        return $name;
    }

    protected function _createRequest($method = '', $params = [])
    {
        $obj = [
            'jsonrpc' => "2.0",
            'method' => $method,
            'params' => $params,
            'id' => 1
        ];
        return $obj;
    }

    protected function _exec($params)
    {
        $paramsStr = json_encode($params);

        var_dump($params);

        curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $paramsStr);
        curl_setopt($this->_curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: '.strlen($paramsStr)));

        $result = curl_exec($this->_curl);
        file_put_contents('/tmp/slackpi', $result);
        return $result;
    }


}

