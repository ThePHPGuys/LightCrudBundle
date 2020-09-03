<?php

namespace TPG\LightCrudBundle\DataLoader;


interface Metadata
{
    public function isAssociationProperty(string $resourceClass, string $property): bool;

    public function getAssociationTargetClass(string $resourceClass, string $property): string;

    public function isValueObjectProperty(string $resourceClass, string $property): bool;

    public function getPrimaryKeyProperty(string $resourceClass): string;

}