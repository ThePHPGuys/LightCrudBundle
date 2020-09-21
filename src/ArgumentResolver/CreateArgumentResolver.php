<?php

namespace TPG\LightCrudBundle\ArgumentResolver;

use Exception;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Serializer\SerializerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use TPG\LightCrudBundle\Annotation\AnnotationReader;
use TPG\LightCrudBundle\Exception\RequestValidationException;
use TPG\LightCrudBundle\JsonBodySerializable;

class CreateArgumentResolver implements ArgumentValueResolverInterface
{
    /**
     * @var SerializerInterface
     */
    private $serializer;
    /**
     * @var ValidatorInterface
     */
    private $validator;
    /**
     * @var AnnotationReader
     */
    private $annotationReader;

    public function __construct(
        SerializerInterface $serializer,
        ValidatorInterface $validator,
        AnnotationReader $annotationReader
    ) {
        $this->serializer = $serializer;
        $this->validator = $validator;
        $this->annotationReader = $annotationReader;
    }

    public function supports(Request $request, ArgumentMetadata $argument): bool
    {
        return $this->isValidRequest($request) && $this->isValidClass($argument->getType());
    }

    private function isValidRequest(Request $request): bool
    {
        return 'json' === $request->getContentType() && $request->isMethod(Request::METHOD_POST);
    }

    private function isValidClass(string $class): bool
    {
        return in_array(JsonBodySerializable::class, class_implements($class), true);
    }

    /**
     * @param Request $request
     * @param ArgumentMetadata $argument
     * @return \Generator|iterable
     * @throws RequestValidationException
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        $controllerMethod = $this->getControllerMethod($request);
        $serializationGroups = $this->annotationReader->getEntitySerialization(...$controllerMethod);
        $context = [
            'groups' => $serializationGroups ? $serializationGroups->groups() : null
        ];
        $entity = $this->serializer->deserialize($request->getContent(), $argument->getType(), 'json', $context);
        $validationGroups = $this->annotationReader->getEntityValidation(...$controllerMethod);
        $violations = $this->validator->validate($entity, null, $validationGroups ? $validationGroups->groups() : null);
        RequestValidationException::assertValid($violations);

        yield $entity;
    }

    private function getControllerMethod(Request $request)
    {
        $controller = explode('::', $request->attributes->get('_controller'));
        if (is_array($controller)) {
            return $controller;
        }
        return [$controller, '__invoke'];
    }
}