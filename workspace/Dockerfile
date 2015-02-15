FROM ubuntu
RUN mkdir -p /var/www/.ssh
RUN echo 'www-data:123456' | chpasswd
#设置www-data用户可以登录
RUN sed -i "s/\/var\/www:\/usr\/sbin\/nologin/\/var\/www:\/bin\/bash/" /etc/passwd
#设置ssh，本地生产key，环境变量填写key
RUN apt-get update && apt-get install -y openssh-server
RUN service ssh start
RUN  mkdir -p /code/local && cd /code/local && ssh-keygen -N "" -t dsa -b 1024 -f rsync-key && cp rsync-key.pub /var/www/.ssh/authorized_keys 

EXPOSE 22
VOLUME ["/code"]

#设置www目录拥有者
#chown -R www-data:www-data  /var/www

#默认ssh没有启动，需要启动
