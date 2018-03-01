<?php
namespace Blog\Post\Service;

use Blog\Post\Repo\ListPostRepo;
use Light\Foundation\App;

class ListPostService extends ServiceBase
{
    protected $listPostRepo;

    public function __construct(ListPostRepo $listPostRepo)
    {
        $this->listPostRepo = $listPostRepo;
    }

    public function list()
    {
        return $this->listPostRepo->list();
    }
}
