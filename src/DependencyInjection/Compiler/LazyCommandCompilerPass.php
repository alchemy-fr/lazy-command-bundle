<?php

namespace Alchemy\LazyCommandBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class LazyCommandCompilerPass implements CompilerPassInterface
{

    public function process(ContainerBuilder $container)
    {
        $proxyFactory = new Definition('Alchemy\LazyCommandBundle\Proxy\ServiceProxyFactory');
        $proxyFactory->addArgument(new Reference('service_container'));

        $container->setDefinition('console.command.argument_proxy_factory', $proxyFactory);

        $commands = $container->findTaggedServiceIds('console.command');

        foreach ($commands as $id => $tags) {
            $definition = $container->getDefinition($id);

            $this->replaceArguments($container, $definition, $id);
        }
    }

    private function replaceArguments(ContainerBuilder $container, Definition $definition, $id)
    {
        $arguments = $definition->getArguments();

        foreach ($arguments as $index => $argument) {
            if (! $argument instanceof Reference) {
                continue;
            }

            $argumentDefinition = $container->getDefinition((string) $argument);
            $lazyArgument = new Definition($argumentDefinition->getClass());
            $lazyKey = '__lazy__' . (string) $argument;

            $container->setDefinition($lazyKey, $lazyArgument);

            $lazyArgument->setFactory(array(new Reference('console.command.argument_proxy_factory'), 'getService'));
            $lazyArgument->setArguments(array(
                $argumentDefinition->getClass(),
                (string) $argument
            ));

            $arguments[$index] = new Reference($lazyKey);
        }

        $definition->setArguments($arguments);
    }
}
