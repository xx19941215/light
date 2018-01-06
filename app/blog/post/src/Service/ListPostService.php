<?php
namespace Blog\Post\Service;

use Blog\Post\Repo\ListPostRepo;

class ListPostService extends ServiceBase
{
    public function list()
    {
        return obj(new ListPostRepo($this->getDmg()))->list();
    }
}