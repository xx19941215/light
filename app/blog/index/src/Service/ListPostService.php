<?php
namespace Blog\Index\Service;

use Blog\Index\Repo\ListPostRepo;

class ListPostService extends ServiceBase
{
    public function list()
    {
        return obj(new ListPostRepo($this->getDmg()))->list();
    }
}
