<?php
namespace Light\Contract\Ui;

use Light\Contract\Controller\ControllerTrait;
use Light\View\RegisterMeta;
use Light\View\RegisterUrl;
use Symfony\Component\HttpFoundation\Response;

abstract class ControllerBase
{
    use ControllerTrait;

    public function getMeta()
    {
        return $this->app->make('meta');
    }

    protected function view($tpl, $data = []) : Response
    {
        return $this->response($this->render($tpl, $data));
    }

    protected function render($tpl, $data = []) : string
    {
        $viewEngine = app('viewManager')->getViewEngine();

        $viewEngine->useData([
            'app' => $this->getApp(),
            'config' => $this->getConfig(),
            'request' => $this->getRequest(),
            'router' => $this->getRouter()
        ]);

        obj(new RegisterMeta($viewEngine))->register($this->getMeta());
        obj(new RegisterUrl($viewEngine))->register($this->getUrlManager());
        return $viewEngine->render($tpl, $data);
    }
}