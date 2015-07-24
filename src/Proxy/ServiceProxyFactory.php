<?php

namespace Alchemy\LazyCommandBundle\Proxy;

use ProxyManager\Factory\LazyLoadingValueHolderFactory;
use Symfony\Component\DependencyInjection\ContainerInterface;

class ServiceProxyFactory
{
    /**
     * @var ContainerInterface
     */
    private $container;

    /**
     * @var LazyLoadingValueHolderFactory
     */
    private $factory;

    /**
     * @param ContainerInterface $container
     */
    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
        $this->factory = new LazyLoadingValueHolderFactory();
    }

    /**
     * @param $class
     * @param $id
     * @return \ProxyManager\Proxy\VirtualProxyInterface
     */
    public function getService($class, $id)
    {
        $container = $this->container;

        return $this->factory->createProxy($class,
            function (& $wrappedObject, $proxy, $method, $parameters, & $initializer) use ($container, $id) {
                $wrappedObject = $container->get($id);
                $initializer = null;
            });
    }
}
