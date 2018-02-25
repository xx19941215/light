<?php
namespace Blog\Post\Ui;

use Blog\Post\Service\FetchPostService;

class FetchPostController extends ControllerBase
{
    public function show(FetchPostService $fetchPostService)
    {
        $id = $this->getParam('id');

        $post = $fetchPostService->fetch($id);

        $prevPost = $fetchPostService->fetchPrev($post)->getItems()->current();

        $nextPost = $fetchPostService->fetchNext($post)->getItems()->current();

        return $this->view('page/post/show', compact('post', 'prevPost', 'nextPost'));
    }
}
