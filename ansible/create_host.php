<?php

$params = $argv;

if (count($params)<2) {
    echo "SYNTAX: php ".$argv[0]. " host1 host2 host3 ...\n\n";
}

array_shift($params);

$servers = [];
for ($i=0; $i<count($params); $i++) {
    // replace unwanted characters
    //$s = preg_replace('/[^0-9:\.]/i', '', $params[$i]);

    $matches = [];
    $result = preg_match('/([0-9]+\.[0-9]+\.[0-9]+\.[0-9]+)(:([0-9]+))?/i', $params[$i], $matches);
    if ($result) {
        $host = $matches[1];
        $port = isset($matches[3])?$matches[3]:22;
        $servers[] = ['host' => $host, 'port' => $port];
    }
}

$content = "[pi]\n";
$i = 0;
foreach ($servers as $server) {
    $i++;
    $host = $server['host'];
    $port = $server['port'];
    $content .= "pi$i ansible_ssh_host=$host ansible_ssh_port=$port ansible_ssh_user=root".PHP_EOL;
}

echo $content;