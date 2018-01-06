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
}