要用某一个伪静态URLRewrite规则，就将相应的规则拷贝到站点根目录


.htaccess 文件是apache环境下伪静态URLRewrite规则；IIS 6环境下访问：http://www.cr173.com/html/32070_1.html，配置成功后可以用该规则

web.config 文件是IIS 7以上版本的伪静态URLRewrite规则

nginx.config 文件是nginx环境下的伪静态URLRewrite规则，要使用nginx伪静态规则，在将nginx.config拷贝到根目录的同时还要在环境的

nginx.config文件中对应引入nginx.config引用方式，

打开 nginx.config 主配置文件，找到
server {
字符串，在下面 root  语句下面加入
加入“include D:/目录/网站路径/nginx.conf”；

保存 重启 nginx 即可
