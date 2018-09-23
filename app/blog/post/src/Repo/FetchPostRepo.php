<?php
namespace Blog\Post\Repo;

use Blog\Post\Model\Post;

class FetchPostRepo extends RepoBase
{
    public function fetch($id)
    {
        return $this->cnn->select()
            ->from('wp_posts')
            ->where('id', '=', $id)
            ->fetchModel(Post::class);
    }

    public function fetchPrev(Post $post)
    {
        $ssb = $this->cnn->select()
            ->from('wp_posts')
            ->where('post_date', '>', $post->post_date)
            ->andWhere('post_status', '=', 'publish')
            ->andWhere('post_type', '=', 'post')
            ->orderBy('post_date', 'asc')
            ->limit(1);

        return $this->dataSet($ssb, Post::class);
    }

    public function fetchNext(Post $post)
    {
        $ssb = $this->cnn->select()
            ->from('wp_posts')
            ->where('post_date', '<', $post->post_date)
            ->andWhere('post_status', '=', 'publish')
            ->andWhere('post_type', '=', 'post')
            ->orderBy('post_date', 'asc')
            ->limit(1);

        return $this->dataSet($ssb, Post::class);
    }
}
