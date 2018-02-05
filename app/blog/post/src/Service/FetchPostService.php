<?php
namespace Blog\Post\Service;

use Blog\Post\Model\Post;
use Blog\Post\Repo\FetchPostRepo;

class FetchPostService extends ServiceBase
{
    public function fetch($id)
    {
        return obj(new FetchPostRepo($this->getDmg()))->fetch($id);
    }

    public function fetchPrev(Post $post)
    {
       return obj(new FetchPostRepo($this->getDmg()))->fetchPrev($post);
    }

    public function fetchNext(Post $post)
    {
       return obj(new FetchPostRepo($this->getDmg()))->fetchNext($post);
    }
}