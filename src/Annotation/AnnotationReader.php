<?php

namespace TPG\LightCrudBundle\Annotation;


use Doctrine\Common\Annotations\Reader;

class AnnotationReader
{
    /**
     * @var Reader
     */
    private $annotationReader;

    public function __construct(Reader $annotationReader)
    {
        $this->annotationReader = $annotationReader;
    }

    public function getEntityClass(string $class, string $method = null): ?EntityClass
    {
        return $this->resolveAnnotation(EntityClass::class, $class, $method);
    }

    /**
     * @param string $annotationClass
     * @param string $class
     * @param string|null $method
     * @return EntityValidation|EntitySerialization|EntityClass|EntityView|null
     * @throws \ReflectionException
     */
    private function resolveAnnotation(string $annotationClass, string $class, string $method = null)
    {
        $classAnnotation = $this->annotationReader->getClassAnnotation(new \ReflectionClass($class), $annotationClass);
        if (!$method) {
            return $classAnnotation;
        }
        $methodAnnotation = $this->annotationReader->getMethodAnnotation(new \ReflectionMethod($class, $method),
            $annotationClass);
        return $methodAnnotation ?? $classAnnotation;
    }

    public function getEntityView(string $class, string $method = null): ?EntityView
    {
        return $this->resolveAnnotation(EntityView::class, $class, $method);
    }

    public function getEntitySerialization(string $class, string $method = null): ?EntitySerialization
    {
        return $this->resolveAnnotation(EntitySerialization::class, $class, $method);
    }

    public function getEntityValidation(string $class, string $method = null): ?EntityValidation
    {
        return $this->resolveAnnotation(EntityValidation::class, $class, $method);
    }
}