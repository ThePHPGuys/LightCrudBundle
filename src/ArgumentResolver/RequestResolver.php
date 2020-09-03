<?php

namespace TPG\LightCrudBundle\ArgumentResolver;


use Doctrine\Common\Annotations\AnnotationReader;
use Doctrine\Common\Annotations\Reader;
use InvalidArgumentException;
use ReflectionClass;
use ReflectionMethod;
use Symfony\Component\HttpFoundation\Request;
use TPG\LightCrudBundle\Annotation\EntityClass;
use TPG\LightCrudBundle\Annotation\EntityView;
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

    public function __construct(Reader $annotationReader)
    {
        //$this->controllerResolver = $controllerResolver;
        $this->annotationReader = $annotationReader;
        //$this->controllerResolver = $controllerResolver;
    }

    public function resolveLoadContext(Request $request): ?LoadContext
    {
        $controller = $this->getControllerMethod($request);
        $entityClass = null;
        /** @var ?EntityClass $methodAnnotation */
        $methodAnnotation = $this->annotationReader->getMethodAnnotation(
            new ReflectionMethod($controller[0], $controller[1]), EntityClass::class
        );

        if ($methodAnnotation) {
            $entityClass = $methodAnnotation;
        }

        if (!$entityClass) {
            /** @var ?EntityClass $classAnnotation */
            $classAnnotation = $this->annotationReader->getClassAnnotation(
                new ReflectionClass($controller[0]), EntityClass::class
            );
            if ($classAnnotation) {
                $entityClass = $classAnnotation;
            }
        }

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
        /** @var EntityView $methodAnnotation */
        $methodAnnotation = $this->annotationReader->getMethodAnnotation(
            new ReflectionMethod($controller[0], $controller[1]), EntityView::class
        );

        if ($methodAnnotation) {
            return ViewBuilder::create()->all(...$methodAnnotation->getFields())->build();
        }

        $classAnnotation = $this->annotationReader->getClassAnnotation(
            new ReflectionClass($controller[0]), EntityView::class
        );

        if ($classAnnotation) {
            return ViewBuilder::create()->all(...$classAnnotation->getFields())->build();
        }

        return null;
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