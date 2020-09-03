<?php

namespace TPG\LightCrudBundle\ArgumentResolver;


use InvalidArgumentException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use TPG\LightCrudBundle\DataLoader\View\View;

class ViewArgumentResolver implements ArgumentValueResolverInterface
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
        return $argument->getType() === View::class;
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $view = $this->argumentsResolver->resolveView($request);
        if (!$view) {
            throw new InvalidArgumentException(sprintf('Unable to resolve view annotations for controller'));
        }
        yield $view;
    }

}