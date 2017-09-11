<?php
namespace Light\Tool;

trait IncludeTrait
{
    public function includeFile($file)
    {
        include $file;
    }
}