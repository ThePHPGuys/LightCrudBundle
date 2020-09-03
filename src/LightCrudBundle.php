<?php

namespace TPG\LightCrudBundle;


use Symfony\Component\HttpKernel\Bundle\Bundle;
use TPG\LightCrudBundle\DependencyInjection\LightCrudBundleExtension;

class LightCrudBundle extends Bundle
{
    public function getContainerExtension()
    {
        return new LightCrudBundleExtension();
    }
}