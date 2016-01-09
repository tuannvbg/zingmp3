<?php

namespace Pimusic;

class MopidyRemote
{

    protected $_curl;
    protected $_config;

    public function __construct($config)
    {
        $this->_config = $config;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_config['url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 1);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10); //timeout in seconds

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

    public function previous()
    {
        $this->_exec($this->_createRequest('core.playback.previous'));
    }

    /**
     *
     * @param null|array $track
     */
    public function play($track = null)
    {
        $request = $this->_createRequest('core.playback.play');
        $request['params']['tl_track'] = $track;
        $this->_exec($request);
    }

    public function resume()
    {
        $this->_exec($this->_createRequest('core.playback.resume'));
    }

    public function pause()
    {
        $this->_exec($this->_createRequest('core.playback.pause'));
    }

    public function isPlaying()
    {
        $response = $this->_exec($this->_createRequest('core.playback.get_state'));
        $responseData = json_decode($response, 1);
        return (isset($responseData['result'])) && $responseData['result'] == 'playing';
    }


    public function clear()
    {
        $this->_exec($this->_createRequest('core.tracklist.clear'));
    }

    public function add($url)
    {
        $request = $this->_createRequest('core.tracklist.add');
        if (is_array($url)) {
            $request['params'] = [
                'uris' => $url
            ];
        }
        else {
            $request['params'] = [
                'uri' => $url
            ];

        }
        $response = $this->_exec($request);

        if (!$this->isPlaying()) {
            $responseData = json_decode($response, 1);
            $item = $responseData['result'][0];
            $this->play($item);
        }
    }

    public function listTracks()
    {

        // Get current track in order to mark active track
        $request = $this->_createRequest('core.playback.get_current_tl_track');
        $response = $this->_exec($request);
        $responseData = json_decode($response, 1);
        $result = $responseData['result'];
        $currentTrackId = null;
        if ($result != '' && isset($result['tlid'])) {
            $currentTrackId = $result['tlid'];
        }


        $request = $this->_createRequest('core.tracklist.get_tl_tracks');
        $response = $this->_exec($request);

        $responseData = json_decode($response, 1);

        $data = [];
        foreach ($responseData['result'] as $item) {
//            print_r($item);die;
            $trackId   = $item['tlid'];
            $trackItem = $item['track'];
            $name = $this->_getTrackName($trackItem);
            $dataItem = ['name' => $name, 'tlid' => $trackId];
            if ($trackId == $currentTrackId)
                $dataItem['isActive'] = 1;

            $data[] = $dataItem;
        }

        return $data;
    }

    public function getCurrent()
    {
        $request = $this->_createRequest('core.playback.get_current_tl_track');
        $response = $this->_exec($request);
        $responseData = json_decode($response, 1);
        $name = $this->_getTrackName($responseData['result']['track']);
        return $name;
    }

    /**
     *
     */
    protected function _getTrackName($track)
    {
        $name = isset($track['name']) ? $track['name'] : 'Unknown';
        // Add artists info to name
        if (isset($track['artists'])) {
            $artists = [];
            foreach ($track['artists'] as $a) {
                $artists[] = $a['name'];
            }

            if (count($artists) > 0) {
                $artistInfo = implode(', ', $artists);
                $name .= ' - '. $artistInfo;
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

        curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $paramsStr);
        curl_setopt($this->_curl, CURLOPT_HTTPHEADER, array(
            'Content-Type: application/json',
            'Content-Length: ' . strlen($paramsStr)));

        $result = curl_exec($this->_curl);

        \App::log($result, 'mopidy_response.log');
        return $result;
    }

    public function getServiceStatus() {
        $return = shell_exec('service mopidy status | head -n 3 | tail -n 1');
        return strpos($return, 'active (running)') !== FALSE;
    }

    public function startService() {
        shell_exec('sudo /etc/init.d/mopidy start');
        sleep(1);
    }

    public function stopService() {
        shell_exec('sudo /etc/init.d/mopidy stop');
        sleep(1);
    }

}

