FROM ubuntu
RUN mkdir -p /var/www/.ssh
RUN echo 'www-data:123456' |chpasswd
RUN curl -L https://github.com/docker/fig/releases/download/1.0.1/fig-`uname -s`-`uname -m` > /usr/local/bin/fig; chmod +x /usr/local/bin/fig
#设置www-data用户可以登录
RUN sed -i "s/\/var\/www:\/usr\/sbin\/nologin/\/var\/www:\/bin\/bash/" /etc/passwd
#设置ssh，本地生产key，环境变量填写key
RUN apt-get update && apt-get install -y openssh-server
RUN service ssh start

#设置www目录拥有者
#chown -R www-data:www-data  /var/www

#默认ssh没有启动，需要启动
