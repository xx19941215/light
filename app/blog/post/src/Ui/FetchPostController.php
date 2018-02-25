<?php
namespace Blog\Post\Ui;

use Blog\Post\Service\FetchPostService;

class FetchPostController extends ControllerBase
{
    public function show()
    {
        $id = $this->getParam('id');

        $post = obj(new FetchPostService($this->app))->fetch($id);

        $prevPost = obj(new FetchPostService($this->app))->fetchPrev($post)->getItems()->current();

        $nextPost = obj(new FetchPostService($this->app))->fetchNext($post)->getItems()->current();

        return $this->view('page/post/show', compact('post', 'prevPost', 'nextPost'));
    }
}
