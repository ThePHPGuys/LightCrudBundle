<?php

namespace TPG\LightCrudBundle\DataLoader;


use InvalidArgumentException;

class SortingOrder
{
    public const ASC = 'ASC';
    public const DESC = 'DESC';

    /**
     * @var string
     */
    private $direction;

    /**
     * @var string
     */
    private $property;

    public function __construct(string $property, $direction = self::ASC)
    {
        if (!in_array($direction, [self::ASC, self::DESC], true)) {
            throw new InvalidArgumentException('Incorrect sorting direction');
        }
        $this->property = $property;
        $this->direction = $direction;
    }

    public function isDescending(): bool
    {
        return !$this->isAscending();
    }

    public function isAscending(): bool
    {
        return $this->direction === self::ASC;
    }

    public function property(): string
    {
        return $this->property;
    }

}