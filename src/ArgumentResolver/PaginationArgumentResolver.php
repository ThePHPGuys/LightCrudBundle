<?php

namespace TPG\LightCrudBundle\ArgumentResolver;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use TPG\LightCrudBundle\DataLoader\Pagination;

class PaginationArgumentResolver implements ArgumentValueResolverInterface
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
        return $argument->getType() === Pagination::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        yield $this->argumentsResolver->resolvePagination($request);
    }

}