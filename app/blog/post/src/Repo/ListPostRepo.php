<?php
namespace Blog\Post\Repo;

use Blog\Post\Dto\PostDto;

class ListPostRepo extends RepoBase
{
    public function list()
    {
        $ssb = $this->cnn->select()
            ->from('wp_posts')
            ->where('post_status', '=', 'publish')
            ->andWhere('post_type', '=', 'post')
            ->limit(10);

        return $this->dataSet($ssb, PostDto::class);
    }
}