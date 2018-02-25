<?php
namespace Blog\Startup\Exceptions;

use Light\Exceptions\NoPermissionException;
use Light\Exceptions\NotLoginException;
use Light\Routing\Exceptions\RouteNotFoundException;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;

class Handler extends \Light\Exceptions\Handler
{
    protected $dontReport = [
        NotLoginException::class
    ];

    public function report(\Exception $e)
    {
        parent::report($e);
    }

    public function render($request, \Exception $e)
    {
        if ($e instanceof NotLoginException) {
            // todo
            return new RedirectResponse(app('urlManager')->routeGet('login'));
        } elseif ($e instanceof RouteNotFoundException) {
            //todo
            return new Response('404', 404);
        } elseif ($e instanceof NoPermissionException) {
            //todo
            return new Response('no-permission', 403);
        }
        return parent::render($request, $e);
    }
}
