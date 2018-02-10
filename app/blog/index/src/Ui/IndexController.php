<?php
namespace Blog\Index\Ui;

use Blog\Index\Service\ListPostService;
use Light\Exceptions\NoPermissionException;
use Light\Exceptions\NotLoginException;
use Light\Http\Exceptions\HttpResponseException;
use Light\Routing\Exceptions\RouteNotFoundException;
use Symfony\Component\HttpFoundation\Response;

class IndexController extends ControllerBase
{
    public function show()
    {
        $posts = obj(new ListPostService($this->app))->list();

        throw new RouteNotFoundException('error');

//        return $this->view('page/index/index', compact('posts'));
    }
}