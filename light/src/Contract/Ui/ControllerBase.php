<?php
namespace Light\Contract\Ui;

use Foil\Engine;
use Foil\Foil;
use Light\Contract\Controller\ControllerTrait;
use Symfony\Component\HttpFoundation\Response;

abstract class ControllerBase
{
    use ControllerTrait;

    protected function view($tpl, $data = []) : Response
    {
        return $this->response($this->render($tpl, $data));
    }

    protected function render($tpl, $data = []) : string
    {
        $viewEngine = $this->getViewEngine();
        $viewEngine->useData([
            'app' => $this->getApp(),
            'config' => $this->getConfig(),
            'request' => $this->getRequest(),
            'router' => $this->getRouter()
        ]);

        $this->engineRegister($viewEngine);

        return $viewEngine->render($tpl, $data);
    }

    protected function engineRegister($viewEngine)
    {
        if (!$viewEngine) {
            throw new \Exception('view engine cannot be empty');
        }
    }

    protected function getViewEngine() : Engine
    {
        $requestApp = $this->getRequest()->getRoute()->getApp();

        $baseDir = $this->config->get('baseDir');

        $folders[] = [$baseDir . '/resource/view'];
        $folders[] = $baseDir . $this->config->get("app.{$requestApp}.dir") . "/view";

        return Foil::boot([
            'folders' => $folders,
            'autoescape' => false,
            'ext' => 'phtml'
        ])->engine();
    }
}