<?php
namespace Blog\Post\Service;

use Blog\Post\Repo\FetchPostRepo;

class FetchPostService extends ServiceBase
{
    public function fetch($id)
    {
        return obj(new FetchPostRepo($this->getDmg()))->fetch($id);
    }
}