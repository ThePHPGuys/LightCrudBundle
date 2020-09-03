<?php

namespace TPG\LightCrudBundle\Annotation;

use InvalidArgumentException;
use function get_class;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class EntityClass
{
    /**
     * @var string
     */
    private $class;

    public function __construct(array $data)
    {
        if (!isset($data['value']) || !$data['value'] || !is_string($data['value'])) {
            throw new InvalidArgumentException(sprintf('Parameter of annotation "%s" cannot be empty.',
                get_class($this)));
        }
        $this->class = $data['value'];
    }

    public function getClass(): string
    {
        return $this->class;
    }

}