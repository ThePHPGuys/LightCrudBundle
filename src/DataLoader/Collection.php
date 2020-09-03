<?php

namespace TPG\LightCrudBundle\DataLoader;


use Doctrine\ORM\Query;
use Doctrine\ORM\Tools\Pagination\Paginator;
use JsonSerializable;
use Traversable;

class Collection implements CollectionResult, JsonSerializable
{
    /**
     * @var Paginator
     */
    private $paginator;

    public function __construct(Query $query)
    {
        $cloned = $query;
        $this->paginator = new Paginator($cloned->setHydrationMode(Query::HYDRATE_ARRAY));
        $this->paginator->setUseOutputWalkers(false);
    }

    public function jsonSerialize()
    {
        return ['data' => (array)$this->getIterator(), 'count' => $this->count()];
    }

    public function getIterator(): Traversable
    {
        return $this->paginator->getIterator();
    }

    public function count(): int
    {
        return $this->paginator->count();
    }


}