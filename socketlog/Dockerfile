FROM ubuntu
RUN apt-get update && \
    apt-get -yq install \
        git \
        nodejs &&\
    rm -rf /var/lib/apt/lists/*
RUN git clone https://github.com/luofei614/SocketLog.git  /socketlog
EXPOSE 1229 1116
WORKDIR /socketlog
CMD nodejs /socketlog/server/index.js 
