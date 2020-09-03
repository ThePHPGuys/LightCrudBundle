<?php

namespace TPG\LightCrudBundle\ArgumentResolver;


use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use TPG\LightCrudBundle\DataLoader\SortingOrder;

class SortingArgumentResolver implements ArgumentValueResolverInterface
{
    /**
     * @var RequestResolver
     */
    private $argumentsResolver;

    public function __construct(RequestResolver $argumentsResolver)
    {
        $this->argumentsResolver = $argumentsResolver;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return SortingOrder::class === $argument->getType() && $request->query->get('sort');
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $sorting = $this->argumentsResolver->resolveSorting($request->query->get('sort'));
        if (!$sorting) {
            throw new InvalidArgumentException(sprintf('Unable to resolve sorting options'));
        }
        yield $sorting;
    }
}