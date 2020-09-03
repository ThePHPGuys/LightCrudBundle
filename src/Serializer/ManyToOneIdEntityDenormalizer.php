<?php

namespace TPG\LightCrudBundle\Serializer;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Serializer\Normalizer\DenormalizerInterface;
use TPG\LightCrudBundle\JsonBodySerializable;

class ManyToOneIdEntityDenormalizer implements DenormalizerInterface
{
    /**
     * @var EntityManagerInterface
     */
    private $em;


    /**
     * EntitySerializer constructor.
     */
    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function supportsDenormalization($data, string $type, string $format = null): bool
    {
        return class_exists($type) && in_array(JsonBodySerializable::class, class_implements($type),
                true) && is_string($data);
    }


    public function denormalize($data, string $type, string $format = null, array $context = [])
    {
        return $this->em->getReference($type, $data);
    }
}