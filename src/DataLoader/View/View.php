<?php

namespace TPG\LightCrudBundle\DataLoader\View;


final class View
{
    private $properties = [];


    public function addProperty(string $name, ?View $view = null): void
    {
        $this->properties[$name] = new ViewProperty($name, $view);
    }

    /**
     * @return array|ViewProperty[]
     */
    public function properties(): array
    {
        return $this->properties;
    }
}