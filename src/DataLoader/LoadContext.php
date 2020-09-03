<?php

namespace TPG\LightCrudBundle\DataLoader;


use TPG\LightCrudBundle\DataLoader\Filter\Condition;
use TPG\LightCrudBundle\DataLoader\Filter\ConditionsGroup;
use TPG\LightCrudBundle\DataLoader\View\View;

class LoadContext
{
    /**
     * @var string
     */
    private $entityClass;

    /**
     * @var ?View
     */
    private $view;

    /**
     * @var ?ConditionsGroup
     */
    private $conditions;

    /**
     * @var ?Pagination
     */
    private $pagination;

    /**
     * @var ?SortingOrder
     */
    private $sorting;

    public function __construct(string $entityClass)
    {
        $this->entityClass = $entityClass;
    }


    public function getView(): ?View
    {
        return $this->view;
    }

    public function setView(?View $view): self
    {
        $this->view = $view;
        return $this;
    }

    public function getSorting(): ?SortingOrder
    {
        return $this->sorting;
    }

    public function setSorting(?SortingOrder $sorting): self
    {
        $this->sorting = $sorting;
        return $this;
    }

    /**
     * @return string
     */
    public function getEntityClass(): string
    {
        return $this->entityClass;
    }

    /**
     * @return Pagination|null
     */
    public function getPagination(): ?Pagination
    {
        return $this->pagination;
    }

    public function setPagination(Pagination $pagination): self
    {
        $this->pagination = $pagination;
        return $this;
    }

    public function addCondition(Condition $condition)
    {
        if (!$this->conditions) {
            $this->conditions = new ConditionsGroup();
        }
        $this->conditions->addCondition($condition);
    }

    public function getConditions(): ?ConditionsGroup
    {
        return $this->conditions;
    }

    public function setConditions(?ConditionsGroup $conditions): self
    {
        $this->conditions = $conditions;
        return $this;
    }

}