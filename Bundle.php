<?php

namespace Bundle\FormBundle;

use Symfony\Foundation\Bundle\Bundle as BaseBundle;
use Symfony\Components\DependencyInjection\ContainerInterface;
use Symfony\Components\OutputEscaper\Escaper;

class Bundle extends BaseBundle
{
    public function boot(ContainerInterface $container)
    {
        Escaper::markClassAsSafe('Bundle\\FormBundle\\Form');
    }
}
