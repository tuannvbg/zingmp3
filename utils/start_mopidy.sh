#!/bin/sh

/etc/init.d/mopidy start
curl -XPOST http://localhost:6681/ --data "silent=1&text=clear"
curl -XPOST http://localhost:6681/ --data "autoplay=1&silent=1&text=http://mp3.zing.vn/album/Em-La-Ba-Noi-Cua-Anh-OST-Miu-Le/ZWZCI7OU.html?st=5" 
