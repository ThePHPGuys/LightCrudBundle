<?php

namespace TPG\LightCrudBundle\ArgumentResolver;

use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use TPG\LightCrudBundle\DataLoader\Filter\ConditionsGroup;

class FilterArgumentResolver implements ArgumentValueResolverInterface
{

    /**
     * @var RequestResolver
     */
    private $requestResolver;

    public function __construct(RequestResolver $requestResolver)
    {
        $this->requestResolver = $requestResolver;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return ConditionsGroup::class === $argument->getType() && $request->query->get('filter');
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $filter = $this->requestResolver->resolveFilter($request->query->get('filter'));
        if (!$filter) {
            throw new InvalidArgumentException(sprintf('Unable to resolve filter options'));
        }
        yield $filter;
    }

}