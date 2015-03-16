# codeEnv

## 说明

   团队中每进入一个新同事都要让他配置半天环境？  团队中每个同事都在本地开发代码， 使用本地的数据库，数据库新增一个字段要通知每个同事？  每个同事配置的本地环境软件版本可以有差异， 经常导致本地能运行上传到服务器不能运行?
   

   为了解决上述问题很多公司要求在开发机服务器上面开发代码， 运行环境一样， 用同一份数据库。 但这样做也有问题： 1，团队中一些同事不习惯在服务器上用vim编程，特别是一些初级程序员、实习生等，对他们来说难度太高。  2， 每加入一位同事， 主管需要在开发机上面为这个同事建立登录账户， 配置vhost访问域名，浪费主管时间。

codeEnv 能解决上述所有问题。 他是一个基于docker的开发环境。 

能让同事在本地编程， 代码实时同步到开发服务器。
同事在本地新建一个代码目录，  服务器就会自动为他启动一个这个代码目录对应的docker容器。

举一个应用场景：

假设团队现在有5个项目， 分别是  www（官网），api（接口）， weixin（微信）， shop（商城），bbs(论坛)， 这5个项目在服务器已经建立好了team镜像（如何建立team镜像，后面会说明。）

开发服务器已经绑定了范域名 *.i.yourdomain.com.

一位叫张三的新同事加入团队，我们给他定义用户名为zhangsan。 然后发给他本地开发同步脚本。  

zhangsan， 在本地建立 www 目录，  服务器会就会自动给他在服务器上建立官网的容器的， 访问地址是  zhangsan.www.i.yourdomain.com

zhangsan, 在本地建立了 api目录，   服务器会就会自动给他在服务器上建立API的容器的， 访问地址是  zhangsan.api.i.yourdomain.com

主管不用给张三建立服务器的登录账户，也不能给张三配置域名。  张三不是在服务器上面编程，而是在本地编程。虽然是在本地编程，但运行环境是在服务器上面的，所有同事用的同样的运行环境，使用同一个数据库
 


## 安装
- 下载codeEnv脚本：
   
`sudo wget -O /usr/local/bin/codeEnv https://raw.githubusercontent.com/luofei614/codeEnv/master/codeEnv ;sudo chmod +x /usr/local/bin/codeEnv ;`

-  安装开发环境：

`codeEnv install  i.yourdomain.com`

- 绑定域名：
上面命令指定了要绑定的域名， i.yourdomain.com，  需要把 i.yourdoamin.com 域名和 \*.yourdoamin.com 泛域名解析到当前服务。

- 下载本地同步脚本
	  下载地址 http://code.i.yourdomain.com/code.zip
	解压下载的压缩包， 编辑 codersyn.sh 脚本， 修改USER变量为自己自定义的英文用户名。 比如我修改USER为luofei。  然后命令行运行 `./codersyn.sh` 
	  此时修改www内的文件， 会自动同步到服务器。 访问服务器网站地址：luofei.www.i.yourdomain.com 
  以后每新来一个同事， 都给他code.zip压缩包， 配置不同的USER变量， 他就有自己的开发环境了。 比如来了个张三， USER变量设置为zhangsan ， 他的服务器访问地址是 zhangsan.www.i.yourdomain.com

  本地环境是支持mac和linux ，本地需要装fswatch和rsync 这两个软件，这两个软件是用于代码实时同步的，  windows因为不能装fswatch和rsync，所以windows的用户暂时只能自行解决，可以用编辑器的sftp插件， 在保存的时候实时同步代码， sftp上传地址 i.yourdomain.com ,端口 222, 用户名 www-data , 密码123456。同步目录 /codeEnv/code/username.dirname

## 添加team镜像

   服务器的/codeEnv/team 目录下是我们团队的运行环境镜像，  www目录是默认的镜像。 我们可以在team目录下添加其他镜像， 比如我们现在要启动API项目，在team目录下建立 名为api的目录， 并在此目录中建立Dockerfile文件。 Dockerfile定义了我们API的开发环境需要装什么软件， 需要做什么配置， 具体Dockerfile的写法见：https://docs.docker.com/reference/builder/ 
    还需要在API目录下建立 fig.yml ,  定义启动这个容器时是的参数，如定义 volumes 虚拟目录， 定义link要连接的其他容器。  具体fig.yml写法见：http://www.fig.sh/yml.html 
    可参考www项目的fig.yml的写法。
     fig.yml 可以使用变量：
     {CONTAINER_NAME} 容器名称，每个同事启动API的容器名称不一样，容器名称必须用这个变量表示，不能在fig.yml文件中写死。
     {WORKDIR}  工作目录， 每个同事的工作目录都不一样， 工作目录中放的是同事从本地机器同步服务器的代码。 我们需要将工作目录映射到docker容器中运行环境的目录中去。  
     VIRTUAL_HOST={USERNAME}.{DIR}.{DOMAIN}  这段代码定义了访问容器的域名。 {USERNAME} 是当前同事的用户名,如zhangsan，  {DIR} 当前目录名称，如api， {DOMAIN} 绑定的域名，如i.yourdomain.com。 通过这些变量组合成一个域名，如： zhangsan.api.i.yourdomain.com

  镜像添加好后，  同事们可以在本地新建一个api文件夹（这个文件夹新建在codesync.sh文件同级目录） ， 然后在api文件夹下放代码， 代码会自动同步到服务器上。 自动建立api定义镜像的容器。 自动根据当前同事的用户名设置访问域名。
    luofei，访问域名就是 luofei.api.i.yourdomain.com
    zhangsan,访问域名就是 zhangsan.api.i.yourdomain.com
    访问域名不一样，但是运行环境是完全一样的，都是出自api镜像。

## codeEnv 默认提供的镜像

   codeEnv 默认提供了
   mysql数据库，容器名是 db  
   phpmyadmin，  访问地址 phpmyadmin.i.yourdomain.com
   memcache, 容器名是 memcache 。
   
   大家可以在自己的team团队镜像中连接db和memcache 。

   socketlog , 服务地址: i.yourdomain.com:1229
