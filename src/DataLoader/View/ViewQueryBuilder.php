<?php

namespace TPG\LightCrudBundle\DataLoader\View;


use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\QueryBuilder;
use Exception;
use TPG\LightCrudBundle\DataLoader\Metadata;

class ViewQueryBuilder
{
    /**
     * @var EntityManagerInterface
     */
    private $em;


    /**
     * @var int
     */
    private $_alias_counter = 0;
    /**
     * @var Metadata
     */
    private $metadata;

    public function __construct(EntityManagerInterface $em, Metadata $metadata)
    {
        $this->em = $em;
        $this->metadata = $metadata;
    }

    public function build(string $entityClass, View $view): QueryBuilder
    {
        //TODO: Create ViewQuery that accepts pagination and filters
        $this->_alias_counter = 0;
        $queryBuilder = $this->em->createQueryBuilder()
            ->from($entityClass, 'e');
        $this->addSelect($queryBuilder, $entityClass, 'e', $view);
        $this->_alias_counter = 0;
        return $queryBuilder;
    }

    private function addSelect(QueryBuilder $queryBuilder, string $entityClass, string $alias, View $view): void
    {
        $selectFields = [];
        foreach ($view->properties() as $property) {
            $propertyName = $property->name();
            if ($property->isSimple()) {
                if ($this->metadata->isAssociationProperty($entityClass,
                        $propertyName) || $this->metadata->isValueObjectProperty($entityClass, $propertyName)) {
                    throw new Exception('You need to specify fields for property ' . $propertyName);
                }
                $selectFields[] = $property->name();
            } else {
                if ($this->metadata->isAssociationProperty($entityClass, $propertyName)) {
                    $joinAlias = $this->getNextAlias();
                    $queryBuilder->join($alias . '.' . $propertyName, $joinAlias);
                    $this->addSelect($queryBuilder,
                        $this->metadata->getAssociationTargetClass($entityClass, $propertyName), $joinAlias,
                        $property->view());
                }

                if ($this->metadata->isValueObjectProperty($entityClass, $propertyName)) {
                    $childPaths = $this->getValueObjectFieldsPaths($property->view());
                    $voPaths = $this->prependPaths($propertyName, $childPaths);
                    foreach ($voPaths as $path) {
                        $selectFields[] = implode('.', $path);
                    }
                }
            }
        }
        $entityPkField = $this->metadata->getPrimaryKeyProperty($entityClass);
        if (!in_array($entityPkField, $selectFields, true)) {
            $selectFields[] = $entityPkField;
        }
        $queryBuilder->addSelect(sprintf('partial %s.{%s}', $alias, implode(',', $selectFields)));
    }

    private function getNextAlias(): string
    {
        return sprintf("_c%d", ++$this->_alias_counter);
    }

    /**
     * @param ViewProperty $property
     * @return array
     */
    private function getValueObjectFieldsPaths(View $view): array
    {
        $paths = [];
        foreach ($view->properties() as $property) {
            if ($property->isSimple()) {
                $paths[] = [$property->name()];
            } else {
                $nestedPaths = $this->getValueObjectFieldsPaths($property->view());
                $paths = array_merge($paths, $this->prependPaths($property->name(), $nestedPaths));
            }
        }
        return $paths;
    }

    private function prependPaths(string $first, array $paths): array
    {
        $prependedPaths = [];
        foreach ($paths as $path) {
            array_unshift($path, $first);
            $prependedPaths[] = $path;
        }
        return $prependedPaths;
    }
}