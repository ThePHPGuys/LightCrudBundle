<?php

namespace TPG\LightCrudBundle\Annotation;

use BadMethodCallException;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class EntityView
{

    /**
     * @var array|string[]
     */
    private $fields;

    public function __construct(array $data)
    {
        if (!$data['fields']) {
            throw new BadMethodCallException('You need to specify fields');
        }
        $this->fields = (array)$data['fields'];
    }

    public function getFields(): array
    {
        return $this->fields;
    }
}