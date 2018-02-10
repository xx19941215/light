<?php
namespace Blog\Auth\Ui;

use Symfony\Component\HttpFoundation\Response;

class LoginController extends ControllerBase
{
    public function show()
    {
        return new Response('hello');
    }
}