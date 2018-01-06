<?php
namespace Blog\Post\Ui;

use Blog\Post\Service\ListPostService;

class ListPostController extends ControllerBase
{
    public function show()
    {
        $posts = obj(new ListPostService($this->app))->list();

        return $this->view('page/post/list', compact('posts'));
    }
}