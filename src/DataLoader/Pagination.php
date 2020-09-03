<?php

namespace TPG\LightCrudBundle\DataLoader;


class Pagination
{
    public $limit;
    public $offset;

    public function __construct(int $limit, int $offset = 0)
    {
        $this->limit = $limit;
        $this->offset = $offset;
    }

}