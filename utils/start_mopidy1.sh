#!/bin/sh

echo "Starting mopidy"
/etc/init.d/mopidy start
sleep 5

echo "Resetting volume to 40%"
curl -X POST -H Content-Type:application/json -d '{"method": "core.mixer.set_volume", "jsonrpc": "2.0", "params": {"volume": 40 }, "id": 1 }' http://10.100.20.198:6680/mopidy/rpc
echo ""

echo "Cleaning playlist"
curl -XPOST http://localhost:6681/ --data "silent=1&text=clear"
echo ""

echo "Playing default playlist"
curl -XPOST http://localhost:6681/ --data "autoplay=1&silent=1&text=http://mp3.zing.vn/playlist/pimusic-morning-beesybee/IOWC6UUZ.html" 
echo ""
