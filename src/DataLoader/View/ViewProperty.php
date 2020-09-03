<?php

namespace TPG\LightCrudBundle\DataLoader\View;


final class ViewProperty
{
    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $query;

    /**
     * @var View
     */
    private $view;

    public function __construct(string $name, ?View $view = null)
    {
        $this->name = $name;
        $this->view = $view;
    }

    public function isSimple(): bool
    {
        return !$this->view;
    }

    public function name(): string
    {
        return $this->name;
    }

    public function view(): ?View
    {
        return $this->view;
    }


}