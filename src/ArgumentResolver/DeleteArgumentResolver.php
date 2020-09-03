<?php

namespace TPG\LightCrudBundle\ArgumentResolver;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityNotFoundException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use TPG\LightCrudBundle\JsonBodySerializable;

class DeleteArgumentResolver implements ArgumentValueResolverInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        return 'json' === $request->getContentType()
            && $request->isMethod(Request::METHOD_DELETE)
            && $request->attributes->has('id')
            && $this->isValidClass($argument->getType());

    }

    private function isValidClass(string $class): bool
    {
        return in_array(JsonBodySerializable::class, class_implements($class), true);
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $entity = $this->em->find($argument->getType(), $request->attributes->get('id'));

        if ($entity === null) {
            throw new EntityNotFoundException();
        }
        yield $entity;
    }
}