<?php
namespace Blog\Index\Ui;

use Blog\Index\Service\ListPostService;

class IndexController extends ControllerBase
{
    public function show()
    {
        $posts = obj(new ListPostService($this->app))->list();

        foreach ($posts->getItems() as $post)
        {
            echo $post->post_title . PHP_EOL;
        }
        exit;
    }
}