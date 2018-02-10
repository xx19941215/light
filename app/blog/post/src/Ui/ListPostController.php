<?php
namespace Blog\Post\Ui;

use Blog\Post\Service\ListPostService;
use Light\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpFoundation\Response;

class ListPostController extends ControllerBase
{
    public function show()
    {
        $page = $this->request->get('page') ?: 1;
        $posts = obj(new ListPostService($this->app))->list();

        if ($page) {
            $posts->setCurrentPage($page);
        }

        $response = new Response(HttpResponseException::class);
        throw new HttpResponseException($response);

//        return $this->view('page/post/list', compact('posts'));
    }
}