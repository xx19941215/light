# Building a production oriented PHP framework from scratch

It is clear that making wheels is one of the better ways to learn a programming language, not a waste of time.

How can then build a production oriented PHP framework? The general process is as follows:


```
Entry file ----> Load Composer vendor class and function
           ----> Register error(and exception) function
           ----> Load config file
           ----> Request
           ----> Router
           ----> (Controller <----> Model)
           ----> View Or Json
```

In addition to this, we also need unit testing, some auxiliary scripts, and so on. The final directory of my framework is as follows:

#  Project Directory Structure


```
├── app [Application directory]
├── bin [Auxiliary scripts directory]
├── bootstrap [Light bootstrap directory]
│ └── app.php [Light App bootstrap file]
├── config [Core Config directory]
│ └── .gitignore
│ └── app.php[app config]
│ └── config.php [app base config]
│ └── database.php [database config]
│ └── i18n.php [i18n config]
│ └── session.php [session config]
│ └── site.php [site config]
│ └── swoole.php [swoole config]
├── light [Light Framework Core directory]
├── public [public directory]
│ ├── index.php [entry file]
│ ├── css [css resource directory]
│ ├── js [javascript resource directory]
├── resources [resource directory]
│ ├── assets [front resource directory]
│       └── sass [sass resource directory]
│              └── app.scss[entry sass file]
│              └── …
│       └── js [javascript resource directory]
│              └── app.js[entry js file]
│              └── …
│ ├── views [PHP view resource directory]
│       └── layouts [layout directory]
│       └── page [page directory]
storage [Other Framework resource directory]
├── framework [framework cache directory]
│             └──cache [cache directory]
│                   └── router.php [router cache]
│                   └── config.php [config cache]
├── logs [log directory]
│             └──error.log [error log]
│             └──light.log [light log]
│             └──access.log.gz [access log]
│             └──swoole.log [swoole log]
tests [Unit test directory]
vendor [composer vendor directory]
.env.example [the environment variables example file]
.gitignore [git ignore config file]
LICENSE [lincese file]
composer.json [composer file]
composer.lock [composer lock file]
package.json [package.json file]
phpunit.xml [phpunit config file]
README-CN.md [readme chinese]
README.md [readme]
webpack.config.js [webpack config file]
yarn.lock [yarn lock file]
```
# Lifecycle：
![](http://blog.xiaoxiao.work/wp-content/uploads/2018/02/lifecycle.png)

# Module description：

## Entrance file

```PHP
// Load the bootstrap file
$app = require __DIR__ . '/../bootstrap/app.php';

//Build Request
$request = new \Light\Http\Request(
    $_GET,
    $_POST,
    array(),
    $_COOKIE,
    $_FILES,
    $_SERVER
);

//Light app handle request
$response = $app->handle($request);
//Send Response
$response->send();
```
[public/index.php](https://github.com/xx19941215/light/blob/master/public/index.php)

##   Error&Exception Handle Module

- Error:

Register a function by used set_error_handler to handle error, but it can't handle the following error, E_ERROR, E_PARSE, E_CORE_ERROR, E_CORE_WARNING, E_COMPILE_ERROR, E_COMPILE_WARNING and the E_STRICT produced by the file which called set_error_handler function. So, we need use register_shutdown_function and error_get_last to handle this finally error which set_error_handler can't handle. When the framework running, we can handle the error by ourself, such as, give a friendly error messge for client.

- Exception:

Register a function by used set_exception_handler to handle the exception which is not be catched, which can give a friendly error messge for client.

[light/Concerns/RegistersExceptionHandlers.php](https://github.com/xx19941215/light/blob/master/light/src/Concerns/RegistersExceptionHandlers.php)

## Config Module 

Load the framework and user defined configuration file.
```php
class Config implements \ArrayAccess
{
    
}
```
The Config class implements ArrayAccess. So we can access configuration files in a way similar to access an array.

[light/Config/Config.php](https://github.com/xx19941215/light/blob/master/light/src/Config/Config.php)

## Router Module
Basic usage:
```
$this
    //Route site
    ->site('www')
    //Route privilege
    ->access('public')
    //GET Method
    ->get(
    //Request path
        '/',
    //Route aliases
        'index',
    //Route controller
        'Blog\Index\Ui\IndexController@show'
    );
```
In Light, multi site routing configuration is supported by default. It can be configured in [`site.php`](https://github.com/xx19941215/light/blob/master/config/site.php). Can be used in routing

Use the `site` method to set the site that the current.
  
[app/blog/post/setting/router/post.php](https://github.com/xx19941215/light/blob/master/app/blog/post/setting/router/post.php)

Light's router is based on [`nikic/fast-route`](https://github.com/nikic/FastRoute)。

[light/src/Routing/Router.php](https://github.com/xx19941215/light/blob/master/light/src/Routing/Router.php)

## ORM

Object Relation Mapping(ORM), its main role is in programming, the concept of object-oriented database table with the corresponding concept. For example, I define an object, it corresponds to a table, an instance of this object corresponds to a record in the table. In the framework, chain encapsulation of common SQL operations is implemented. Subsequent operation will be completed through the operation of the database object.
                              The basic usage of a SELECT query in Light

In Repo subclass
```php
$ssb = $this->cnn->select()
            ->from('wp_posts')
            ->where('post_status', '=', 'publish')
            ->andWhere('post_type', '=', 'post')
            ->orderBy('post_date', 'desc')
            ->limit(15);
```

In places that do not inherit from Repo, such as View, you can use the DB's [Facade](https://github.com/xx19941215/light/blob/master/light/src/Support/Facades/DB.php)

```php
$ssb = DB::select()
            ->from('wp_posts')
            ->where('post_status', '=', 'publish')
            ->andWhere('post_type', '=', 'post')
            ->orderBy('post_date', 'desc')
            ->limit(15);
```

Returns [`DataSet`](https://github.com/xx19941215/light/blob/master/light/src/Database/DateSet.php) and the DataSet instance will do some simple processing of the data returned by `$ssb` .

[]()

[app/blog/post/src/repo/ListPostRepo.php](https://github.com/xx19941215/light/blob/master/app/blog/post/src/Repo/ListPostRepo.php)

```php
return $this->dataSet($ssb, Post::class);
```

In places that do not inherit from Repo，you can use[`collect`](https://github.com/xx19941215/light/blob/master/light/src/Support/functions.php)to resolve DataSet。

Print the title of article.
```php
foreach ($posts->getItems() as $post) {
    echo $post->title . PHP_EOL;
}
```
## Service Container Module

Light's core is a service container. The service container provides the entire range of services needed in the framework.
In our day-to-day development, creating objects is so ubiquitous that it's so familiar to them that it's very tedious, and it's pretty bad for each time an object needs to be new-handed. But more seriously, the loosely coupled, less intrusive principle that we have always advocated has become useless in this situation.

When it comes to service containers, what I have to mention is control inversion, or IOC for short, which is a commonly used design pattern. Dependency injection is a way to implement IOCs.

The basic attributes and methods in Light's service container are as follows
```
- attributes
    + bindings
    + _instance
    # instances
    # aliases
    
- methods
    + bind
    + getClosure
    + make
    # getConcrete
    + build
    # getDependencies
    # resolveClass
    # isBuildable
    + singleton
    + _setInstance
    + instance
    + isShared
    # dropStaleInstances
    + getAlias
    + bound
    + isAlias
    + _getInstance
    + call
```

[light/Foundation/App.php](https://github.com/xx19941215/light/blob/master/light/src/Foundation/App.php)

## MVC To MVSC

Software from the development of a transaction to deal with many affairs, between the affairs of the inclusion, order, primary and secondary relationship, become more and more complex. Because of the huge data and logic, in Light, in addition to the traditional MVC three-tier structure, it is recommended to add a new layer of Service to handle tedious business logic.
This time, Controller layer can display different views according to the different devices, but Service layer business logic has been reused.
In Light, an App can consist of the following structure:

- Model: The mapping exists as a field of the database table
- Repo: execute Model's crud operation
- Controller: handles the presentation of data and views distributed by the Service
- Service: Processing business logic
- View: view

[Service/FetchPostService.php](https://github.com/xx19941215/light/blob/master/app/blog/post/src/Service/FetchPostService.php)

## View & Meta & Trans
Light's view layer supports layout, components and other flexible organizational view layer structure, the underlying directly using [`foil`](https://github.com/FoilPHP/Foil) to achieve,
You can place your view file in the project's resource/views folder.

Light customizes the `title`,` description`, `keywords` for each page in the view layer, as well as supports internationalization of other text.
[light/Meta/Meta.php](https://github.com/xx19941215/light/blob/master/light/src/Meta/Meta.php)

## FrontEnd Module

Light builds `Javascript` with` webpack` and compiles the `scss` file with` node-sass` to produce front-end style files.
The front-end resource files are placed in `resource/assets`.

### build steps

1.Install the dependencies 
```
yarn install
```

2.FrontEnd build script
```
npm run build: js
npm run build: css
```

Generated resource files will be stored in the `public` folder under the appropriate directory for public access.

## PHPUnit
Based on PHPUnit, Light will continue to refine the test. Run the following command to get started
```php
./vendor/bin/phpunit
```
Test example:

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
