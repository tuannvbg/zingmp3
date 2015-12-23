#!/bin/sh

IP=$(ifconfig | awk '/inet addr/{print substr($2,6)}' | grep -v '127.0.0.1' |  sed ':a;N;$!ba;s/\n/;/g')

curl http://pi.tungns.com/update.php?ip=$IP

