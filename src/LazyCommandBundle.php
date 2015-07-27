<?php

namespace Alchemy\LazyCommandBundle;

use Alchemy\LazyCommandBundle\DependencyInjection\Compiler\LazyCommandCompilerPass;
use Symfony\Component\DependencyInjection\Compiler\PassConfig;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class LazyCommandBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new LazyCommandCompilerPass(), PassConfig::TYPE_AFTER_REMOVING);
    }
}
