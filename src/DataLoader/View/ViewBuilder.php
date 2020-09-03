<?php

namespace TPG\LightCrudBundle\DataLoader\View;


class ViewBuilder
{
    /**
     * @var ViewBuilder[]
     */
    private $builders;

    /**
     * @var string[]
     */
    private $properties;

    public function __construct()
    {
        $this->builders = [];
        $this->properties = [];
    }

    public static function create(): self
    {
        return new static();
    }

    public function all(string ...$properties): self
    {
        array_map(function (string $p) {
            $this->add($p);
        }, $properties);
        return $this;
    }

    public function add(string $property): self
    {
        $parts = explode('.', $property);
        $this->addParts($parts);
        return $this;
    }

    private function addParts(array $parts): void
    {
        $propName = array_shift($parts);

        if (!in_array($propName, $this->properties, true)) {
            $this->properties[] = $propName;
        }

        if (count($parts) > 0) {
            $this->getBuilderFor($propName)->addParts($parts);
        }
    }

    private function getBuilderFor(string $property)
    {
        return $this->builders[$property] ?? $this->builders[$property] = new static();
    }

    public function build(): View
    {
        $view = new View();
        foreach ($this->properties as $property) {
            $nestedView = null;
            if (array_key_exists($property, $this->builders)) {
                $nestedView = $this->getBuilderFor($property)->build();
            }
            $view->addProperty($property, $nestedView);
        }
        return $view;
    }
}