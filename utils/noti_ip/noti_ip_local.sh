#!/bin/sh

BASE_DIR=/data/utils
DATA_FILE="$BASE_DIR/noti_ip.dat"

if [ -f $DATA_FILE ]; then
    OLD_IP=$(cat $DATA_FILE)
fi

NEW_IP=$(ifconfig | awk '/inet addr/{print substr($2,6)}' | grep -v '127.0.0.1' |  sed ':a;N;$!ba;s/\n/;/g')

if [ "$OLD_IP" != "$NEW_IP" ]; then
    echo "IP has changed"
    echo "$NEW_IP" > $DATA_FILE

    OLD_IP=$NEW_IP

    echo "Sending notification email"
    echo "New IP: $NEW_IP \nAt: $(date +"%T")" | mail -s "Raspberry's IP has changed" -aFrom:"Raspberry Pi<pi.nstung@gmail.com>" nstung@gmail.com
    
fi

# echo "IP: $OLD_IP"
