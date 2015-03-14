#!/bin/bash
chown www-data:www-data /app -R
env | grep S_  | sed  "s/^/export /g" >> /etc/apache2/envvars 
source /etc/apache2/envvars
exec apache2 -D FOREGROUND
