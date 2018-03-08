<?php
namespace Light\View;

use Foil\Engine;
use Foil\Foil;
use Light\Foundation\App;

class ViewManager
{
    protected $app;

    public function __construct(App $app)
    {
        $this->app = $app;
    }

    public function getViewEngine() : Engine
    {
        $basePath = $this->app->basePath;

        $folders[] = $basePath . '/resources/views';
        return Foil::boot([
            'folders' => $folders,
            'autoescape' => false,
            'ext' => 'phtml'
        ])->engine();
    }
}
