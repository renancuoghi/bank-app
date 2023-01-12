<?php

namespace App\Domain\Shared\Helper;

use JsonSerializable;

class Paginator implements JsonSerializable
{
    private $total = 0;

    private $items = [];

    private $page_size = 10;

    private $total_pages = 1;

    public function __construct($items = [], $total = 0, $page_size = 10)
    {
        $this->setItems($items);
        $this->setPage_size($page_size);
        if ($total > 0) {
            $this->setTotal($total);
            $this->setTotal_pages(ceil($total/$page_size));
        }
    }


    public function getTotal(): int
    {
        return $this->total;
    }

    public function setTotal(int $total)
    {
        $this->total = $total;
    }

    public function setItems($items)
    {
        $this->items = $items;
    }

    public function getItems()
    {
        return $this->items;
    }

    public function getPage_size(): int
    {
        return $this->page_size;
    }

    public function setPage_size(int $page_size)
    {
        $this->page_size = $page_size;
    }

    public function getTotal_pages(): int
    {
        return $this->total_pages;
    }

    public function setTotal_pages(int $total_pages)
    {
        $this->total_pages = $total_pages;
    }

    public function jsonSerialize()
    {
        $stdClass = new \stdClass();
        $stdClass->total = $this->getTotal();
        $stdClass->items = $this->getItems();
        $stdClass->page_size = $this->getPage_size();
        $stdClass->total_pages = $this->getTotal_pages();
        return $stdClass;
    }
}
