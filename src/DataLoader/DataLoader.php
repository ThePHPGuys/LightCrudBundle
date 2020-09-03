<?php

namespace TPG\LightCrudBundle\DataLoader;


use Doctrine\Common\Collections\Criteria;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;
use TPG\LightCrudBundle\DataLoader\Filter\ConditionsCriteriaBuilder;
use TPG\LightCrudBundle\DataLoader\Filter\ConditionsGroup;
use TPG\LightCrudBundle\DataLoader\View\ViewQueryBuilder;

class DataLoader
{

    /**
     * @var ViewQueryBuilder
     */
    private $viewQueryBuilder;
    /**
     * @var Metadata
     */
    private $metadata;
    /**
     * @var ConditionsCriteriaBuilder
     */
    private $conditionBuilder;

    public function __construct(
        ViewQueryBuilder $viewQueryBuilder,
        ConditionsCriteriaBuilder $conditionBuilder,
        Metadata $metadata
    ) {
        $this->viewQueryBuilder = $viewQueryBuilder;
        $this->metadata = $metadata;
        $this->conditionBuilder = $conditionBuilder;
    }

    public function loadCollection(LoadContext $loadContext): CollectionResult
    {
        $queryBuilder = $this->createQueryBuilder($loadContext);
        $this->applyPagination($queryBuilder, $loadContext->getPagination());
        $this->applySorting($queryBuilder, $loadContext->getSorting());
        return new Collection($queryBuilder->getQuery());
    }

    private function createQueryBuilder(LoadContext $loadContext): QueryBuilder
    {
        if (!$loadContext->getView()) {
            throw new InvalidArgumentException('View is missing in LoadContext');
        }

        $queryBuilder = $this->viewQueryBuilder->build($loadContext->getEntityClass(), $loadContext->getView());

        $this->applyConditions($queryBuilder, $loadContext->getConditions());
        return $queryBuilder;
    }

    private function applyConditions(QueryBuilder $queryBuilder, ?ConditionsGroup $conditions): void
    {
        if (!$conditions) {
            return;
        }
        $criteria = $this->conditionBuilder->build($conditions);
        $queryBuilder->addCriteria($criteria);
    }

    private function applyPagination(QueryBuilder $queryBuilder, ?Pagination $pagination): void
    {
        if (!$pagination) {
            return;
        }
        $queryBuilder->setMaxResults($pagination->limit);
        $queryBuilder->setFirstResult($pagination->offset);

    }

    private function applySorting(QueryBuilder $queryBuilder, ?SortingOrder $sorting): void
    {
        if (!$sorting) {
            return;
        }
        $queryBuilder->addCriteria(Criteria::create()->orderBy([$sorting->property() => $sorting->isAscending() ? 'ASC' : 'DESC']));
    }

    public function loadOne(string $id, LoadContext $loadContext): OneResult
    {
        $queryBuilder = $this->createQueryBuilder($loadContext);
        $this->applyById($queryBuilder, $loadContext->getEntityClass(), $id);
        return new One($queryBuilder->getQuery());
    }

    private function applyById(QueryBuilder $queryBuilder, string $entityClass, string $id): void
    {
        $idProperty = $this->metadata->getPrimaryKeyProperty($entityClass);
        $queryBuilder->where(sprintf('e.%s = :id', $idProperty))->setParameter('id', $id);
    }

}