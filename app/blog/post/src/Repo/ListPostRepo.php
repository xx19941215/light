<?php
namespace Blog\Post\Repo;

use Blog\Post\Model\Post;
use Light\Support\Facades\DB;

class ListPostRepo extends RepoBase
{
    public function list()
    {
        $ssb = $this->cnn->select()
            ->from('wp_posts')
            ->where('post_status', '=', 'publish')
            ->andWhere('post_type', '=', 'post')
            ->orderBy('post_date', 'desc')
            ->limit(15);

//        $ssb = DB::select()
//            ->from('wp_posts')
//            ->where('post_status', '=', 'publish')
//            ->andWhere('post_type', '=', 'post')
//            ->orderBy('post_date', 'desc')
//            ->limit(15);

        return $this->dataSet($ssb, Post::class);
//        return collect($ssb, Post::class);
    }
}
