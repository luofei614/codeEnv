#!/bin/sh
USER=luofei
PWD=$( pwd | sed "s/\//#/g")
fswatch -0 . |  while read -d "" file ; do \
    FILE_PATH=$(echo $file | sed "s/\//#/g")
    SHORT_FILE_PATH=$(echo $FILE_PATH | sed "s/$PWD//g")
    DIR=`echo $SHORT_FILE_PATH|awk -F '#' '{print $2}'`
    #如果是目录
    if [  -d "$DIR" ]; then  
        date +%H:%M:%S && /usr/bin/rsync -iru --exclude .git  --delete -e 'ssh -i ./rsync-key -p 22' $DIR/ www-data@103.227.76.203:/code/$USER.$DIR
    fi  
done
