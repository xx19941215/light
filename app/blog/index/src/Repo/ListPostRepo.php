<?php
namespace Blog\Index\Repo;

use Blog\Index\Model\Post;

class ListPostRepo extends RepoBase
{
    public function list()
    {
        $ssb = $this->cnn->select()
            ->from('wp_posts')
            ->where('post_status', '=', 'publish')
            ->andWhere('post_type', '=', 'post')
            ->orderBy('post_date', 'desc');

        return $this->dataSet($ssb, Post::class);
    }
}
