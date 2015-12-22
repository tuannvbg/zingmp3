<?php

namespace Pimusic;

class Queue
{

    protected $_config;

    protected $_gearman;

    public function __construct($config)
    {
        $this->_gearman = new \GearmanClient();
        $this->_gearman->addServer(); //localhost
    }

    public function add($queueName, $data)
    {
        return $this->_gearman->doBackground($queueName, json_encode($data));
    }

}
