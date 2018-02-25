<?php
namespace Light\Tool;

trait IncludeTrait
{
    public function includeFile($file)
    {
        include $file;
    }

    public function includeDir($dir)
    {
        if (!file_exists($dir)) {
            // todo
            return;
        }

        foreach (scandir($dir) as $file) {
            if (pathinfo($file, PATHINFO_EXTENSION) == 'php') {
                $this->includeFile($dir . '/' . $file);
            }
        }
    }
}
