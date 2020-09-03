<?php

namespace TPG\LightCrudBundle\ArgumentResolver;


use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use TPG\LightCrudBundle\DataLoader\LoadContext;

class LoadContextArgumentResolver implements ArgumentValueResolverInterface
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
        return $argument->getType() === LoadContext::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $loadContext = $this->requestResolver->resolveLoadContext($request);
        if (!$loadContext) {
            throw new InvalidArgumentException(sprintf('Unable to resolve load context'));
        }
        yield $loadContext;
    }

}