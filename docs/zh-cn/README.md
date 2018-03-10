# 从零开始一步步构建面向生产的PHP框架

需要明确的是，造轮子是学习一门编程语言比较好的方式之一，而不是浪费时间。

那怎样才能构建一个自己的面向生产的PHP框架呢？大致流程如下：

```
　　　　
入口文件　----> 载入composer自动加载文件 
        ----> 注册错误(和异常)处理函数
        ----> 加载配置文件
        ----> 请求
        ----> 路由　
        ---->（控制器 <----> 数据模型）
        ----> 视图渲染数据
```

除此之外我们还需要单元测试、一些辅助脚本等。最终我的框架目录如下：

#  框架目录一览

```
├── app [PHP应用目录]
├── bin [构建命令库]
├── bootstrap [App脚手架]
│ └── app.php [构建App实例]
├── config [核心配置目录]
│ └── .gitignore
│ └── app.php[app配置]
│ └── config.php [app基础配置]
│ └── database.php [数据库配置]
│ └── i18n.php [国际化配置]
│ └── session.php [会话配置]
│ └── site.php [站点配置]
│ └── swoole.php [swoole配置]
├── light [Light Framework核心目录]
├── public [公共资源目录，暴露到万维网]
│ ├── index.php [后端入口文件]
│ ├──css [css资源目录]
│ ├── javascripts [js资源目录]
├── resources [资源目录]
│ ├── assets [前端资源目录]
│       └── sass [sass资源目录]
│              └── app.scss[入口sass目录]
│              └── …
│       └── js [js资源目录]
│              └── app.js[入口js目录]
│              └── …
│ ├── views [PHP视图资源目录]
│       └── layouts [布局目录]
│       └── page [page目录]
storage [框架其他文件存储目录]
├── framework [framework缓存目录]
│             └──cache [缓存目录]
│                   └── router.php [路由缓存]
│                   └── config.php [全局配置缓存]
├── logs [日志目录]
│             └──error.log [错误日志]
│             └──light.log [app日志]
│             └──access.log.gz [访问日志]
│             └──swoole.log [swoole日志]
tests [单元测试目录]
├── demo [模块名称]
│ └── DemoTest.php [测试演示]
├── TestCase.php [测试用例]
vendor [composer目录]
.env [环境变量文件]
.gitignore [git忽略文件配置]
LICENSE [lincese文件]
composer.json [composer配置文件]
composer.lock [composer lock文件]
package.json [前端依赖配置文件]
phpunit.xml [phpunit配置文件]
README-CN.md [中文版readme文件]
README.md [readme文件]
webpack.config.js [webpack配置文件]
yarn.lock [yarn　lock文件]

```
# 框架生命周期：
![](http://blog.xiaoxiao.work/wp-content/uploads/2018/02/lifecycle.png)
# 框架模块说明：

##  入口文件

```PHP
// 载入框架运行文件
$app = require __DIR__ . '/../bootstrap/app.php';

//构造Request
$request = new \Light\Http\Request(
    $_GET,
    $_POST,
    array(),
    $_COOKIE,
    $_FILES,
    $_SERVER
);

//app处理request
$response = $app->handle($request);
//发送response
$response->send();
```
[public/index.php](https://github.com/xx19941215/light/blob/master/public/index.php)
##  错误和异常模块

脚本运行期间：

- 错误:

通过函数set_error_handler注册用户自定义错误处理方法，但是set_error_handler不能处理以下级别错误，E_ERROR、 E_PARSE、 E_CORE_ERROR、 E_CORE_WARNING、 E_COMPILE_ERROR、 E_COMPILE_WARNING，和在 调用 set_error_handler() 函数所在文件中产生的大多数 E_STRICT。所以我们需要使用register_shutdown_function配合error_get_last获取脚本终止执行的最后错误，目的是对于不同错误级别和致命错误进行自定义处理，例如返回友好的提示的错误信息。

- 异常:

通过函数set_exception_handler注册未捕获异常处理方法，目的捕获未捕获的异常，例如返回友好的提示和异常信息。


[light/Concerns/RegistersExceptionHandlers.php](https://github.com/xx19941215/light/blob/master/light/src/Concerns/RegistersExceptionHandlers.php)

##  配置文件模块

加载框架自定义和用户自定义的配置文件。
```php
class Config implements \ArrayAccess
{
    
}
```
Config类实现了ArrayAccess，可以使用类似访问数组一样的方式访问配置文件。

[light/Config/Config.php](https://github.com/xx19941215/light/blob/master/light/src/Config/Config.php)

##  路由模块
用法如下
```
$this
    //路由站点
    ->site('www')
    //权限设置
    ->access('public')
    //GET请求
    ->get(
    //请求路径
        '/',
    //路由别名
        'index',
    //分发控制器
        'Blog\Index\Ui\IndexController@show'
    );
```
在Light中，默认支持多站点路由配置。可以在[`site.php`](https://github.com/xx19941215/light/blob/master/config/site.php)配置。在路由中可以
使用`site`方法设定当前路由所属的站点。使用`access`方法规定当前路由的接入权限，Light支持`public`、`login`和`admin`以及更加精确的`acl`过滤方式。`get`设定当前路由可以使用GET方法访问，并可以设置访问路径，路由别名和最终的控制器。

[app/blog/post/setting/router/post.php](https://github.com/xx19941215/light/blob/master/app/blog/post/setting/router/post.php)

Light底层的路由基于[`nikic/fast-route`](https://github.com/nikic/FastRoute)实现。

[light/src/Routing/Router.php](https://github.com/xx19941215/light/blob/master/light/src/Routing/Router.php)

## 对象关系映射

Object Relation Mapping, 其主要作用是在编程中，把面向对象的概念跟数据库中表的概念对应起来。举例来说就是，我定义一个对象，那就对应着一张表，这个对象的实例，就对应着表中的一条记录。在框架中，实现了对常用SQL操作的链式封装。后续将会通过操作对象直接完成数据库操作。
在Light中一个SELECT查询的基本用法

在Repo的子类中
```php
$ssb = $this->cnn->select()
            ->from('wp_posts')
            ->where('post_status', '=', 'publish')
            ->andWhere('post_type', '=', 'post')
            ->orderBy('post_date', 'desc')
            ->limit(15);
```

在没有继承Repo的地方(例如View中)，可以通过DB的[`Facade`](https://github.com/xx19941215/light/blob/master/light/src/Support/Facades/DB.php)使用

```php
$ssb = DB::select()
            ->from('wp_posts')
            ->where('post_status', '=', 'publish')
            ->andWhere('post_type', '=', 'post')
            ->orderBy('post_date', 'desc')
            ->limit(15);
```

返回[`DataSet`](https://github.com/xx19941215/light/blob/master/light/src/Database/DateSet.php)，DataSet实例将对`$ssb`返回的数据做一些简单的处理。

[]()

[app/blog/post/src/repo/ListPostRepo.php](https://github.com/xx19941215/light/blob/master/app/blog/post/src/Repo/ListPostRepo.php)

```php
return $this->dataSet($ssb, Post::class);
```

在没有继承Repo的地方，可以通过[`collect`](https://github.com/xx19941215/light/blob/master/light/src/Support/functions.php)函数生成DataSet。

输出文章标题列表
```php
foreach ($posts->getItems() as $post) {
    echo $post->title . PHP_EOL;
}
```
##  服务容器模块

Light的核心就是一个服务容器。服务容器提供了整个框架中需要的一系列服务。
在我们的日常开发中，创建对象的操作随处可见以至于对其十分熟悉的同时又感觉十分繁琐，每次需要对象都需要亲手将其new出来，这是相当糟糕的。但更为严重的是，我们一直倡导的松耦合，少入侵原则，这种情况下变得一无是处。

说到服务容器，不得不提到的就是控制反转，简称为IOC，这是一个常用的设计模式。依赖注入是实现IOC的一种方式。

Light核心实(chao)现(xi)了一个小巧的服务容器。基本attributes和methods如下

```
- attributes
    + bindings 抽象和实现之间的映射数组
    + _instance 容器静态实例
    # instances 服务实例数组
    # aliases 服务别名
    
- methods
    + bind 绑定abstract和concrete
    + getClosure 返回服务Closure便于统一管理
    + make 生成服务
    # getConcrete 返回服务的具体实现
    + build 构建服务对象
    # getDependencies 从服务容器拿到build时的dependencies
    # resolveClass 获得给定class的实例
    # isBuildable 判断当前服务是否可以构建
    + singleton 单例缓存服务
    + _setInstance 单例缓存当前容器实例
    + instance 保存实例
    + isShared 当前服务实例是否可以共享
    # dropStaleInstances 删除过期绑定
    + getAlias 获得服务别名
    + bound 服务是否已经注册
    + isAlias 服务是否有别名
    + _getInstance 获得容器实例
    + call 获取容器服务，实现控制器调用时的依赖注入
```

[light/Foundation/App.php](https://github.com/xx19941215/light/blob/master/light/src/Foundation/App.php)

## MVC To MVSC

软件从处理一件事务发展到了要处理许多事务，各事务间有包含、顺序、主次等等的关系，变得越来越复杂。因为数据与逻辑庞大了，所以在Light中，除了传统的MVC三层结构，推荐新增一层Service层来处理繁琐的业务逻辑。
这个时候，Controller层可以根据设备的不同展示不同的View，但是Service层的业务逻辑得到了复用。
在Light中，App可以由如下结构组成：

- Model: 作为数据库表的字段的映射存在
- Repo: 执行Model的crud操作
- Controller: 处理Service分发的数据和view的展示
- Service: 处理业务逻辑
- View: 视图

[Service/FetchPostService.php](https://github.com/xx19941215/light/blob/master/app/blog/post/src/Service/FetchPostService.php)

## View & Meta & Trans
Light的视图层支持布局、组件等方式灵活的组织视图层结构，底层直接使用[`foil`](https://github.com/FoilPHP/Foil)实现，
你可以在项目的`resource/views`文件夹下放置你的视图文件。

Light在视图层支持每一个页面自定义国际化的`title`、`description`、`keywords`，也支持将其他文本作国际化的输出。

[light/Meta/Meta.php](https://github.com/xx19941215/light/blob/master/light/src/Meta/Meta.php)

## 前端构建

Light使用`webpack`构建`Javascript`，使用`node-sass`编译`scss`文件生产前端样式文件。
前端资源文件统一放置于`resource/assets`内。

### build步骤

1.依赖安装
```
yarn install
```

2.前端构建脚本
```
npm run build:js
npm run build:css
```

生成的资源文件将会保存在`public`文件夹下的相应目录进行公开访问。


## PHPUnit
基于PHPUnit，Light将会持续完善测试。运行如下的命令即可开始
```php
./vendor/bin/phpunit
```

测试栗子

```php
class ConfigTest extends TestCase
{
    protected $config;
    protected $data;

    public function setUp()
    {
        $this->config = new Config($this->data = [
            'foo' => 'bar',
            'bar' => 'baz',
            'baz' => 'bat',
            'null' => null,
            'associate' => [
                'x' => 'xxx',
                'y' => 'yyy',
            ],
            'array' => [
                'aaa',
                'zzz',
            ],
            'x' => [
                'z' => 'zoo',
            ],
        ]);

        parent::setUp();
    }

    public function testConstruct()
    {
        $this->assertInstanceOf(Config::class, $this->config);
    }
}
```

[light/tests/ConfigTest.php](https://github.com/xx19941215/light/blob/master/light/tests/Config/ConfigTest.php)


# 如何使用?

1.通过composer新建项目light-project
```
composer create-project xx19941215/light-project light-project  && cd light-project
```

2.安装前后端依赖
```
composer install && npm i
```

3.Nginx server 配置, 请根据你自己项目路径和PHP服务修改相关信息。
```
server {
    listen  80;
    #listen [::]:80 ipv6only=on;
    server_name www.light-project.test
		static.light-projecr.test;
    
    #return 301 https://$server_name$request_uri;

    index   index.php index.html;
    root    /path/to/light-project/public;

    access_log  /path/to/light-project/storage/logs/access.log.gz combined gzip;
    error_log /path/to/light-project/storage/logs/error.log;

    client_max_body_size 20M;

    gzip  on;
    gzip_min_length 1k;
    gzip_buffers 4 16k;
    gzip_http_version 1.0;
    gzip_comp_level 6;
    gzip_types  text/plain application/javascript application/x-javascript text/javascript text/xml text/css;
    gzip_disable "MSIE [1-6]\.";
    gzip_vary on;

    location / {
        try_files $uri $uri/ /index.php?$args;
    }

    location ~ \.php(/|$) {
        try_files $uri = 404;
        include fastcgi.conf;
        fastcgi_connect_timeout 60;
        fastcgi_send_timeout 180;
        fastcgi_read_timeout 180;
        fastcgi_buffer_size 128k;
        fastcgi_buffers 4 256k;
        fastcgi_busy_buffers_size 256k;
        fastcgi_temp_file_write_size 256k;

        fastcgi_index   index.php;
        #fastcgi_pass   unix:/run/php/php7.0-fpm.sock;
        fastcgi_pass    127.0.0.1:9000;

        location ~ /\.ht {
            deny all;
        }
    }
}

server { 
    listen      80; 
    server_name static.light-project.test; 
 
    index index.html index.htm; 
    root /path/to/light-project/public/static; 
 
    access_log /path/to/light-project/storage/logs/static.access.log.gz combined gzip; 
    error_log /path/to/light-project/storage/logs/static.error.log; 
 
    client_max_body_size 20M; 
 
    location / { 
    } 
 
    location ~* \.(eot|svg|ttf|woff|woff2)$ { 
        if ($http_origin ~* '^https?://[^/]+\.light-project\.test$') { 
            add_header Access-Control-Allow-Origin $http_origin; 
        } 
    } 
 
    location ~ /\.ht { 
        deny all; 
    } 
}
```

4.Hosts 配置 
```
127.0.0.1 www.light-project.test
127.0.0.1 static.light-project.test
```

5.复制.env.example 为.env, 配置 `APP_BASE_HOST` 和相关的数据库配置.
```
APP_DEBUG=true
APP_BASE_HOST=light-project.test
DB_HOST=localhost
DB_USERNAME=root
DB_PASSWORD=qwertyuiop
DB_DATABASE=light
CACHE_DRIVER=redis
I18N=false
```


6.确保以下服务正常开启
```
redis
mysql
php-fpm
nginx
```


7.在数据库中增加meta表.

```sql
CREATE TABLE `meta` (
  `metaId` varbinary(21) NOT NULL,
  `key` varchar(20) NOT NULL,
  `localeKey` varchar(20) NOT NULL,
  `value` varchar(20) NOT NULL,
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `changed` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`metaId`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
```

8.打开浏览器访问 http://www.light-project.test/

# TODO

- Console Application
- Database Migration
- A Instant Message Application based on Light and Workman
- Security
- Session