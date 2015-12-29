<?php

namespace Pimusic;

class Slack
{

    protected $_config;

    protected $_curl;

    protected $_requestData = [];

    public function __construct($config)
    {
        $this->_config = $config;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $config['webhook_url']);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, "POST");
        $this->_curl = $ch;

        \App::registerEvent('playlist_fetch_song', Array($this, 'notifySongInPlaylistAdd'));
        \App::registerEvent('playlist_fetch', Array($this, 'notifyPlaylistFetch'));
        \App::registerEvent('download_cache_miss', Array($this, 'onDownloadCacheMiss'));

    }

    public function __destruct()
    {
        curl_close($this->_curl);
    }

    /**
     * Send a text to Slack via Incomming webhook
     *
     * @param $text
     * @return mixed|void
     */
    public function send($text)
    {
    	if (isset($this->_requestData['silent'])) return ;

        $payloadData = ['text' => $text];
        $payloadStr  = json_encode($payloadData);

//        var_dump($payloadStr);

        curl_setopt($this->_curl, CURLOPT_POSTFIELDS, $payloadStr);
        curl_setopt($this->_curl, CURLOPT_HTTPHEADER, array(
                'Content-Type: application/json',
                'Content-Length: ' . strlen($payloadStr))
        );

        $response = curl_exec($this->_curl);
        \App::log($response, 'slack.log');

        return $response;
    }

    public function notifySongInPlaylistAdd($params) {
        $index = $params['id'];
        $item  = $params['item'];

//        if ($index == 1) {
//            $this->notifySongsAdded(Array($item));
//        }
//        else {
            $text = "  + :musical_note: " . $item['title'];
            $this->send($text);
//        }

        $this->setNotified();
    }

    public function notifySongsAdded($songList) {
        $text = '';

        if (isset($this->_requestData['user_name']))
            $text .= '@'.$this->_requestData['user_name'].': ';

        $text .= "Đã nhận hàng :ok_hand:\n\n";
        $titles = [];
        foreach ($songList as $item) {
            $titles[] = "  + :musical_note: " . $item['title'];
        }
        $text .= implode("\n", $titles);

        $this->send($text);
    }

    public function notifyPlaylistFetch($playlist) {
        $text = '';

        if (isset($this->_requestData['user_name']))
            $text .= '@'.$this->_requestData['user_name'].': ';

        $text .= "Đã nhận playlist :ok_hand:\n\n";
        $text .= "  *".$playlist['title']."*";
        $this->send($text);
    }

    public function onDownloadCacheMiss($params) {
        if (isset($this->_requestData['notified_patient']) && $this->_requestData['notified_patient'] == true) {
        }
        else {
            $this->_requestData['notified_patient'] = true;
            $text = "Be patient...";
            $this->send($text);
        }
    }

    public function setRequestData($data) {
        $this->_requestData = $data;
    }

    public function setNotified() {
        $this->_requestData['notified'] = true;
    }

    public function isNotified() {
        return isset($this->_requestData['notified']) && $this->_requestData['notified'] == true;
    }

    public function showTrackList($list) {
        if (count($list)<=0) return ;

        $line = [];
        $i = 0;
        foreach ($list as $item) {
            $i++;
            $name = $item['name'];
            if (isset($item['isActive']) && $item['isActive'])
                $line[] = "*$i. $name* :arrow_forward:";
            else
                $line[] = "$i. $name";
        }

        $this->send(implode("\n", $line));
    }

}
