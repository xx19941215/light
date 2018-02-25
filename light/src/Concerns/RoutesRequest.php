<?php
namespace Light\Concerns;

use Light\Http\Request;
use Light\I18n\Exceptions\LocaleNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;

trait RoutesRequest
{
    protected $currentRoute;
    protected $dispatcher;

    public function handle(Request $request)
    {
        $this->instance(Request::class, $request);
        $request->setSiteManager($this->make('siteManager'));
        $request->setLocaleManager($this->make('localeManager'));

        if ($this->make('config')->get('config.i18n')) {
            $parsed = $this->parsePathInfo($request->getPathInfo());
            $path = $parsed['path'];
            $localeKey = $parsed['localeKey'];
            if (!$localeKey) {
                if ($path !== '/') {
                    //todo
                    throw new LocaleNotFoundException('localeNotFound');
                }

                return $this->gotoLocaleResponse($request);
            }

            if (!$path) {
                return $this->gotoLocaleResponse($request);
            }

            $this->make('localeManager')->setLocaleKey($localeKey);
            $request->setPath($path);
        }

        $route = $this->router->dispatch($request);

//        try {
            $this->currentRoute = $route;
            $request->setRoute($route);
            return $this->callControllerAction($request);
//        } catch (\Exception $e) {
//            throw $e;
//        }
    }

    protected function gotoLocaleResponse($request)
    {
        if ($localeUrl = $this->getLocaleUrl($request)) {
            return new RedirectResponse($localeUrl);
        }

        //todo
        throw new LocaleNotFoundException('localeNotFound');
    }

    protected function getLocaleUrl(Request $request)
    {
        $protocol = $request->isSecure() ? 'https://' : 'http://';

        $localeUrl = $protocol . $request->getHttpHost() . '/' . $request->getLocaleKey() . '/';
        return $localeUrl;
    }

    protected function callControllerAction(Request $request)
    {
        $route = $request->getRoute();
        list($controllerClass, $method) = explode('@', $route->getAction());
//
//        if (!class_exists($controllerClass)) {
//            throw new \Exception("class not found: $controllerClass");
//        }
//
//        $controller = new $controllerClass($this->make('app'), $request);
//
//        if (!method_exists($controller, $fun)) {
//            throw new \Exception("method not found: $controllerClass::$fun");
//        }
//
//        if ($res = $controller->bootstrap()) {
//            return $res;
//        }
//
//        return $controller->$fun();

        if (! method_exists($instance = $this->make($controllerClass), $method)) {
            //todo
            throw new \Exception('not found');
        }

        return $this->call([$instance, $method]);
    }

    protected function parsePathInfo($pathInfo)
    {
        $config = $this->make('config');

        $path = substr($pathInfo, 1);
        $pos = strpos($path, '/');
        // zh-cn/user/
        if ($pos !== false) {
            $tryLocaleKey = substr($path, 0, $pos);
            if ($locale = $config->get('i18n.locale.available.'.$tryLocaleKey)) {
                $pathInfo = substr($pathInfo, $pos + 1);

                return [
                    'localeKey' => $tryLocaleKey,
                    'path' => $pathInfo,
                ];
            }
        }
        // zh-cn
        if ($locale = $config->get('i18n.locale.available.' . $path)) {
            return [
                'localeKey' => $path,
                'path' => '',
            ];
        }

        // user
        return ['localeKey' => '', 'path' => $pathInfo];
    }
}
