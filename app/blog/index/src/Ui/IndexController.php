<?php
namespace Blog\Index\Ui;

class IndexController extends ControllerBase
{
    public function show()
    {
        return $this->view('page/index/index');
    }
}