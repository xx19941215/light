<?php
namespace Light\View;

use Foil\Contracts\EngineInterface;

class RegisterBase
{
    protected $engine;

    public function __construct(EngineInterface $engine)
    {
        $this->engine = $engine;
    }
}
