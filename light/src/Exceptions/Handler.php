<?php

namespace Light\Exceptions;

use Light\Contract\Debug\ExceptionHandler;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Debug\ExceptionHandler as SymfonyExceptionHandler;
use Symfony\Component\Debug\Exception\FlattenException;
use Whoops\Run;

class Handler implements ExceptionHandler
{
    protected $dontReport = [];

    public function report(\Exception $e)
    {
        if ($this->shouldntReport($e)) {
            return;
        }

        try {
            $logger = app('Psr\Log\LoggerInterface');
        } catch (\Exception $ex) {
            throw $e; // throw the original exception
        }

        $logger->error($e);
    }

    protected function shouldntReport(\Exception $e)
    {
        foreach ($this->dontReport as $type) {
            if ($e instanceof $type) {
                return true;
            }
        }

        return false;
    }

    public function renderForConsole($output, \Exception $e)
    {
        // todo
    }

    public function render($request, \Exception $e): Response
    {
        $headers =  [];
        $statusCode = 500;
        try {
            $content = config('config.debug') && class_exists(Run::class)
                ? $this->renderExceptionWithWhoops($e)
                : $this->renderExceptionWithSymfony($e, config('config.debug'));
        } catch (\Exception $e) {
            $content = $content ?? $this->renderExceptionWithSymfony($e, config('config.debug'));
        }

        // todo
        $response = Response::create($content, $statusCode, $headers);

        return $response;
    }

    protected function renderExceptionWithWhoops(\Exception $e)
    {
        $whoops = new \Whoops\Run;
        $whoops->pushHandler(new \Whoops\Handler\PrettyPageHandler);
        $whoops->writeToOutput(false);
        $whoops->allowQuit(false);
        return $whoops->handleException($e);
    }

    protected function renderExceptionWithSymfony(\Exception $e, $debug)
    {
        return (new SymfonyExceptionHandler($debug))->getHtml(
            FlattenException::create($e)
        );
    }
}
