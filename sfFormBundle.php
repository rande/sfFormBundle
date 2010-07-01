<?php

namespace Bundle\sfFormBundle;

use Symfony\Foundation\Bundle\Bundle as BaseBundle;
use Symfony\Components\DependencyInjection\ContainerInterface;
use Symfony\Components\OutputEscaper\Escaper;

class sfFormBundle extends BaseBundle
{
    public function boot(ContainerInterface $container)
    {
        Escaper::markClassAsSafe('Bundle\\sfFormBundle\\Form');
    }
}
