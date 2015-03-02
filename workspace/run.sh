#!/bin/bash
chown www-data:www-data /app -R
source /etc/apache2/envvars
chown -R www-data:www-data /code
/usr/sbin/sshd -D & 
exec apache2 -D FOREGROUND
