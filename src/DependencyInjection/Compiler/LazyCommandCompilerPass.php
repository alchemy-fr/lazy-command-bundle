<?php

namespace Alchemy\LazyCommandBundle\DependencyInjection\Compiler;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;

/**
 * Makes commands lazy: Symfony only creates a command, and the services it
 * depends on, when its tag holds its name. Runs configure() here to read it.
 */
class LazyCommandCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container): void
    {
        foreach ($container->findTaggedServiceIds('console.command') as $id => $tags) {
            if (isset($tags[0]['command'])) {
                continue;
            }

            $definition = $container->getDefinition($id);
            $class = $container->getParameterBag()->resolveValue($definition->getClass());
            $command = $this->configure($class);

            if ($command === null || !$command->getName()) {
                continue;
            }

            $definition->clearTag('console.command');
            $definition->addTag('console.command', ['command' => $command->getName()]);

            foreach ($command->getAliases() as $alias) {
                $definition->addTag('console.command', ['command' => $alias]);
            }
        }
    }

    private function configure(string $class): ?Command
    {
        try {
            $reflection = new \ReflectionClass($class);
            $command = $reflection->newInstanceWithoutConstructor();
            (new \ReflectionMethod(Command::class, '__construct'))->invoke($command);

            return $command;
        } catch (\Throwable) {
            // configure() needs a dependency: the command stays eager
            return null;
        }
    }
}
