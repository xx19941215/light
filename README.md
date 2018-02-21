# 从零开始一步步构建PHP框架

需要明确的是，造轮子是学习一门编程语言比较好的方式之一，而不是浪费时间。

那怎样才能构建一个自己的PHP框架呢？大致流程如下：

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
##  错误和异常模块

脚本运行期间：

- 错误:

通过函数set_error_handler注册用户自定义错误处理方法，但是set_error_handler不能处理以下级别错误，E_ERROR、 E_PARSE、 E_CORE_ERROR、 E_CORE_WARNING、 E_COMPILE_ERROR、 E_COMPILE_WARNING，和在 调用 set_error_handler() 函数所在文件中产生的大多数 E_STRICT。所以我们需要使用register_shutdown_function配合error_get_last获取脚本终止执行的最后错误，目的是对于不同错误级别和致命错误进行自定义处理，例如返回友好的提示的错误信息。

- 异常:

通过函数set_exception_handler注册未捕获异常处理方法，目的捕获未捕获的异常，例如返回友好的提示和异常信息。

##  配置文件模块

加载框架自定义和用户自定义的配置文件。

##  路由模块

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

## 对象关系映射

Object Relation Mapping, 其主要作用是在编程中，把面向对象的概念跟数据库中表的概念对应起来。举例来说就是，我定义一个对象，那就对应着一张表，这个对象的实例，就对应着表中的一条记录。在框架中，实现了对常用SQL操作的链式封装。后续将会通过操作对象直接完成数据库操作。

##  服务容器模块

什么是服务容器？
Light的核心就是一个服务容器。服务容器提供了整个框架中需要的一系列服务。
在我们的日常开发中，创建对象的操作随处可见以至于对其十分熟悉的同时又感觉十分繁琐，每次需要对象都需要亲手将其new出来，这是相当糟糕的。但更为严重的是，我们一直倡导的松耦合，少入侵原则，这种情况下变得一无是处。

服务容器的意义？
说到服务容器，不得不提到的就是控制反转，简称为IOC，这是一个常用的设计模式。依赖注入是实现IOC的一种方式。

