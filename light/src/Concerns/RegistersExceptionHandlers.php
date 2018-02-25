<?php
namespace Light\Concerns;

use Light\Contract\Debug\ExceptionHandler;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Debug\Exception\FatalErrorException;
use Symfony\Component\Debug\Exception\FatalThrowableError;

trait RegistersExceptionHandlers
{
    public function registerErrorHandling()
    {

        // Report all PHP errors
        error_reporting(-1);

        //使用set_error_handler()函数将错误信息托管至ErrorException
        set_error_handler(function ($level, $message, $file = '', $line = 0) {
            if (error_reporting() & $level) {
                throw new \ErrorException($message, 0, $level, $file, $line);
            }
        });

        set_exception_handler(function ($e) {
            $this->handleUncaughtException($e);
        });

        register_shutdown_function(function () {
            $this->handleShutdown();
        });
    }

    protected function handleShutdown()
    {
        if (! is_null($error = error_get_last()) && $this->isFatalError($error['type'])) {
            $this->handleUncaughtException(new FatalErrorException(
                $error['message'],
                $error['type'],
                0,
                $error['file'],
                $error['line']
            ));
        }
    }

    protected function isFatalError($type)
    {
        $errorCodes = [E_ERROR, E_CORE_ERROR, E_COMPILE_ERROR, E_PARSE];

        if (defined('FATAL_ERROR')) {
            $errorCodes[] = FATAL_ERROR;
        }

        return in_array($type, $errorCodes);
    }

    protected function handleUncaughtException($e)
    {
        $handler = $this->resolveExceptionHandler();
        if ($e instanceof \Error) {
            $e = new FatalThrowableError($e);
        }

        $handler->report($e);

        if ($this->runningInConsole()) {
            $handler->renderForConsole(new ConsoleOutput, $e);
        } else {
            $handler->render($this->make('request'), $e)->send();
        }
    }

    protected function resolveExceptionHandler()
    {
        if ($this->bound(ExceptionHandler::class)) {
            return $this->make(ExceptionHandler::class);
        } else {
            return $this->make('Light\Exceptions\Handler');
        }
    }
}
