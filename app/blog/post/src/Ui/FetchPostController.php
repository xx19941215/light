<?php
namespace Blog\Post\Ui;

use Blog\Post\Service\FetchPostService;

class FetchPostController extends ControllerBase
{
    public function show()
    {
        $id = $this->getParam('id');

        $post = obj(new FetchPostService($this->app))->fetch($id);

        return $this->view('page/post/show', compact('post'));
    }
}