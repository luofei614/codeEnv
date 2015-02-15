#!/bin/bash
mkdir -p /code/local
cd /code/local
ssh-keygen -N "" -t dsa -b 1024 -f rsync-key


