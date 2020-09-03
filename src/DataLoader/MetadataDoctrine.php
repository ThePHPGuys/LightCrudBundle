<?php

namespace TPG\LightCrudBundle\DataLoader;


use Doctrine\ORM\EntityManagerInterface;

class MetadataDoctrine implements Metadata
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function isAssociationProperty(string $resourceClass, string $property): bool
    {
        return $this->em->getClassMetadata($resourceClass)->isSingleValuedAssociation($property);
    }

    public function getAssociationTargetClass(string $resourceClass, string $property): string
    {
        return $this->em->getClassMetadata($resourceClass)->getAssociationTargetClass($property);
    }

    public function isValueObjectProperty(string $resourceClass, string $property): bool
    {
        return array_key_exists($property, $this->em->getClassMetadata($resourceClass)->embeddedClasses);
    }

    public function getPrimaryKeyProperty(string $resourceClass): string
    {
        return $this->em->getClassMetadata($resourceClass)->identifier[0];
    }
}