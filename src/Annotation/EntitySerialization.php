<?php

namespace TPG\LightCrudBundle\Annotation;

use BadMethodCallException;

/**
 * @Annotation
 * @Target({"CLASS", "METHOD"})
 */
class EntitySerialization
{
    private $groups = [];

    public function __construct(array $data)
    {
        if ($data['groups']) {
            if (is_string($data['groups'])) {
                $this->groups = [$data['groups']];
            } else {
                $this->groups = $data['groups'];
            }
        }
    }

    public function groups(): array
    {
        return $this->groups;
    }
}