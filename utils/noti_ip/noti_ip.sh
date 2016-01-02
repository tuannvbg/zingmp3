#!/bin/sh

# wait few sec for network becomes ready
sleep 15

# Print the IP address
_IP=$(hostname -I) || true
if [ "$_IP" ]; then
  printf "My IP address is %s\n" "$_IP"
fi

IP=$(ifconfig | awk '/inet addr/{print substr($2,6)}' | grep -v '127.0.0.1' |  sed ':a;N;$!ba;s/\n/;/g')

curl http://pi.tungns.com/update.php?ip=$IP

