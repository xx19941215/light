<?php
namespace Light\Database;

use Light\Contract\Database\SqlBuilder\SelectSqlBuilderInterface;
use Light\Database\Exception\DatabaseException;

class DateSet implements \JsonSerializable
{
    protected $ssb;
    protected $dtoClass;

    protected $currentPage;
    protected $itemCount;
    protected $pageCount;
    protected $countPerPage = 10;

    public function __construct(SelectSqlBuilderInterface $ssb, $dtoClass = '')
    {
       if (!class_exists($dtoClass)) {
           throw new DatabaseException("$dtoClass not exist");
       }

       $this->dtoClass = $dtoClass;
       $this->ssb = $ssb;
    }

    public function setCountPerPage($count) : self
    {
        $this->countPerPage = $count;
        return $this;
    }

    public function getItems()
    {
        $this->ssb
            ->limit($this->countPerPage)
            ->offset(($this->getCurrentPage() - 1) * $this->countPerPage);

        if ($this->dtoClass) {
            return $this->ssb->listDto($this->dtoClass);
        }

        return $this->ssb->listObj();
    }

    public function setCurrentPage($page) : self
    {
        $this->currentPage = $page;
        return $this;
    }

    public function getCurrentPage()
    {
        if ($this->currentPage) {
            return $this->currentPage;
        }

        $this->setCurrentPage(1);
        return $this->currentPage;
    }

    public function getItemCount() : int
    {
        if ($this->itemCount) {
            return $this->itemCount;
        }

        return $this->ssb->count();
    }

    public function getPageCount() : int
    {
        if ($this->pageCount) {
            return $this->pageCount;
        }

        $this->pageCount = ceil($this->getItemCount() / $this->countPerPage);
        return $this->pageCount;
    }

    public function jsonSerialize() : array
    {
        $arr = [];
        foreach ($this->getItems() as $item) {
            $arr[] = $item;
        }

        return $arr;
    }
}