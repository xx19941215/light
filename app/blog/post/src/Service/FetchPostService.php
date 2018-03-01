<?php
namespace Blog\Post\Service;

use Blog\Post\Model\Post;
use Blog\Post\Repo\FetchPostRepo;
use Light\Foundation\App;

class FetchPostService extends ServiceBase
{
    protected $fetchPostRepo;

    public function __construct(FetchPostRepo $fetchPostRepo)
    {
        $this->fetchPostRepo = $fetchPostRepo;
    }

    public function fetch($id)
    {
        return $this->fetchPostRepo->fetch($id);
    }

    public function fetchPrev(Post $post)
    {
        return $this->fetchPostRepo->fetchPrev($post);
    }

    public function fetchNext(Post $post)
    {
        return $this->fetchPostRepo->fetchNext($post);
    }
}
