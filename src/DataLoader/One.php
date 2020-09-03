<?php

namespace TPG\LightCrudBundle\DataLoader;


use Doctrine\ORM\Query;
use JsonSerializable;

class One implements OneResult, JsonSerializable
{
    /**
     * @var Query
     */
    private $query;

    public function __construct(Query $query)
    {
        $this->query = $query;
    }

    public function jsonSerialize()
    {
        return $this->query->setHydrationMode(Query::HYDRATE_ARRAY)->getOneOrNullResult();
    }
}