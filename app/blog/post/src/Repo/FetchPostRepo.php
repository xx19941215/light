<?php
namespace Blog\Post\Repo;

use Blog\Post\Dto\PostDto;

class FetchPostRepo extends RepoBase
{
    public function fetch($id)
    {
        return $this->cnn->select()
            ->from('wp_posts')
            ->where('id', '=', $id)
            ->fetchDto(PostDto::class);

    }

    public function fetchPrev(PostDto $post)
    {
        $ssb = $this->cnn->select()
            ->from('wp_posts')
            ->where('post_date', '>', $post->post_date)
            ->andWhere('post_status', '=', 'publish')
            ->andWhere('post_type', '=', 'post')
            ->orderBy('post_date', 'desc')
            ->limit(1);

       return $this->dataSet($ssb, PostDto::class);
    }

    public function fetchNext(PostDto $post)
    {
        $ssb = $this->cnn->select()
            ->from('wp_posts')
            ->where('post_date', '<', $post->post_date)
            ->andWhere('post_status', '=', 'publish')
            ->andWhere('post_type', '=', 'post')
            ->orderBy('post_date', 'desc')
            ->limit(1);

       return $this->dataSet($ssb, PostDto::class);
    }
}