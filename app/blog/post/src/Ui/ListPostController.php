<?php
namespace Blog\Post\Ui;

use Blog\Post\Service\ListPostService;

class ListPostController extends ControllerBase
{
    public function show()
    {
        $page = $this->request->get('page') ?: 1;
        $posts = obj(new ListPostService($this->app))->list();

        if ($page) {
            $posts->setCurrentPage($page);
        }

        return $this->view('page/post/list', compact('posts'));
    }
}