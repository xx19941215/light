<?php
namespace Light\Http\Exceptions;

use Symfony\Component\HttpFoundation\Response;

class HttpResponseException extends \RuntimeException
{
    protected $response;

    public function __construct(Response $response)
    {
        $this->response = $response;
    }

    public function gerResponse()
    {
        return $this->response;
    }
}
