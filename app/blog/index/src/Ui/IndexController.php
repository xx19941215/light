<?php
namespace Blog\Index\Ui;

use Blog\Index\Service\ListPostService;

class IndexController extends ControllerBase
{
    public function show()
    {
        $posts = obj(new ListPostService($this->app))->list();

        return $this->view('page/index/index', compact('posts'));
    }
}