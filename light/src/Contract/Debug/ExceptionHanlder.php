<?php
namespace Light\Contract\Debug;

interface ExceptionHandler
{
    public function report(\Exception $e);

    public function render($request, \Exception $e);

    public function renderForConsole($output, \Exception $e);
}
