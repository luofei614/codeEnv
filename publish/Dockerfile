#代码发布系统
#和coding结合，内部关联git下载代码， 然后移动带发布目录， 发布目录和外部目录相关联
FROM ubuntu:12.04
#RUN sed -i "s/archive\.ubuntu\.com/mirrors\.163\.com/g" /etc/apt/sources.list
RUN apt-get update && \
    apt-get -yq install \
        curl \
        apache2 \
        libapache2-mod-php5 \
        php5-mysql \
        php5-gd \
        php5-curl \
        php-pear \
        php5-memcache \
        make \
        wget \
        php-apc && \
        
    rm -rf /var/lib/apt/lists/*
#安装php-taint
RUN pecl install taint
RUN echo "extension=taint.so\ntaint.enable=On" > /etc/php5/conf.d/taint.ini
RUN sed -i "s/variables_order.*/variables_order = \"EGPCS\"/g" /etc/php5/apache2/php.ini
#虚拟目录设置， 发布接口放在虚拟目录中
RUN echo "Alias /publish_api /publish_api \n\
   <Directory /publish_api> \n\
    Options Indexes FollowSymLinks MultiViews\n\
    AllowOverride None\n\
    DirectoryIndex index.php\n\
    Order allow,deny\n\
    allow from all\n\
   </Directory>\n\
" > /etc/apache2/conf.d/publish_api.conf
ADD publish_api /publish_api

#gearman 队列
RUN apt-get update &&  apt-get -yq  install libboost-all-dev gperf libevent1-dev libcloog-ppl0 && \
wget https://launchpad.net/gearmand/1.2/1.1.8/+download/gearmand-1.1.8.tar.gz && \ 
tar zxvf gearmand-1.1.8.tar.gz  && \
cd gearmand-1.1.8/ && \
./configure && \
make && \
make install && \ 
pecl install gearman
RUN echo "extension=gearman.so" > /etc/php5/conf.d/gearman.ini 

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
#安装phpunit
RUN composer global require 'phpunit/phpunit=4.5.*'
RUN ln -s /root/.composer/vendor/bin/phpunit  /usr/local/bin/phpunit

#rewrite
RUN sed -i "s/AllowOverride None/AllowOverride All/g" /etc/apache2/sites-available/default
RUN ln -s /etc/apache2/mods-available/rewrite.load /etc/apache2/mods-enabled/rewrite.load

ADD run.sh /run.sh
ADD test.sh /test.sh
RUN chmod 755 /*.sh

# Configure /app folder with sample app
RUN mkdir -p /app && rm -fr /var/www && ln -s /app /var/www
RUN mkdir /publish_codedir
VOLUME ["/app","/publish_codedir"]

#代码仓库
RUN apt-get update && apt-get install -yq ca-certificates git-core ssh
ENV HOME /root
ADD ssh/ /root/.ssh/
RUN chmod 600 /root/.ssh/*
RUN ssh-keyscan -p 22 coding.net > /root/.ssh/known_hosts
#在容器内自己git clone
#RUN git clone git@coding.net:luofei614/test.git /app
#解决svn不能提交中文文件的问题
RUN locale-gen zh_CN.UTF-8



EXPOSE 80
WORKDIR /app
CMD ["/run.sh"]
