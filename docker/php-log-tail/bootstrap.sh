#!/bin/sh

if [ -f "/var/log/shared/pipe" ]; then
    mkfifo /var/log/shared/pipe
    chmod 777 /var/log/shared/pipe
fi

exec tail -f /var/log/shared/pipe
