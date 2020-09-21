<?php

namespace TPG\LightCrudBundle\ArgumentResolver;


use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use TPG\LightCrudBundle\Annotation\AnnotationReader;
use TPG\LightCrudBundle\DataLoader\Filter\ConditionsGroup;
use TPG\LightCrudBundle\DataLoader\LoadContext;
use TPG\LightCrudBundle\DataLoader\Pagination;
use TPG\LightCrudBundle\DataLoader\SortingOrder;
use TPG\LightCrudBundle\DataLoader\View\View;
use TPG\LightCrudBundle\DataLoader\View\ViewBuilder;
use function is_array;

class RequestResolver
{
    private const DEFAULT_LIMIT = 50;

    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    public function __construct(AnnotationReader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function resolveLoadContext(Request $request): ?LoadContext
    {
        $controller = $this->getControllerMethod($request);
        $entityClass = $this->annotationReader->getEntityClass($controller[0], $controller[1]);
        if (!$entityClass) {
            return null;
        }
        $loadContext = new LoadContext($entityClass->getClass());
        $loadContext->setPagination($this->resolvePagination($request));
        $loadContext->setSorting($this->resolveSorting($request->query->get('sort')));
        $loadContext->setView($this->resolveView($request));
        $loadContext->setConditions($this->resolveFilter($request->query->get('filter')));
        return $loadContext;
    }

    private function getControllerMethod(Request $request)
    {
        $controller = explode('::', $request->attributes->get('_controller'));
        if (is_array($controller)) {
            return $controller;
        }
        return [$controller, '__invoke'];
    }

    public function resolvePagination(Request $request): Pagination
    {
        $limit = $request->query->getInt('limit', self::DEFAULT_LIMIT);
        $offset = $request->query->getInt('offset', 0);
        return new Pagination($limit, $offset);
    }

    public function resolveSorting(?string $sort): ?SortingOrder
    {
        if (!$sort) {
            return null;
        }

        if (in_array($sort[0], ['+', '-'], true)) {
            $direction = $sort[0] === '+' ? SortingOrder::ASC : SortingOrder::DESC;
            $property = substr($sort, 1);
        } else {
            $direction = SortingOrder::ASC;
            $property = $sort;
        }

        return new SortingOrder($property, $direction);
    }

    public function resolveView(Request $request): ?View
    {
        $controller = $this->getControllerMethod($request);
        $entityView = $this->annotationReader->getEntityView($controller[0], $controller[1]);
        if (!$entityView) {
            return null;
        }
        return ViewBuilder::create()->all(...$entityView->getFields())->build();
    }

    public function resolveFilter(?string $filterString): ?ConditionsGroup
    {
        if (!$filterString) {
            return null;
        }
        $filtersArray = json_decode($filterString, true);
        if (!is_array($filtersArray)) {
            return null;
        }
        try {
            return ConditionsGroup::createFromArray($filtersArray);
        } catch (InvalidArgumentException $e) {
            return null;
        }
    }
}