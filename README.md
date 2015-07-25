# lazy-command-bundle

Bundle for better dependency injection with Symfony commands

## Installing

As simple as running a command in your terminal:

`composer require alchemy/lazy-command-bundle`.

Of course, you will also need to have [Composer](https://getcomposer.org/) installed beforehand.

## Usage

Just add the bundle to your kernel. For now, it's zero-config, hassle free proxy goodness.

## Background

Using commands as services in Symfony can be quite a pain as soon as your dependencies start using connections
to various resources (database, message queues, what not...), as all commands have to be instantiated in order
for a Symfony Console application to run properly.

This bundle is an attempt to solve this by replacing all the service dependencies of your commands by lazy proxies
(using the excellent ProxyManager library), ensuring that external resources are only acquired as needed.

Another approach could be to mark the dependencies of your commands as lazy using Symfony DI's built-in features,
but this approach is not optimal when your dependencies are re-used in multiple contexts (ie, a service that
is used both by a console application and a web application) and a proxy is not required (or worse, is a performance
penalty) in all contexts.

## How it works

The bundle adds a compiler pass to your service container's build process, replacing all service references by lazy
proxies for services that are tagged with the `console.command` tag.

There is no performance penalty in HTTP contexts (unless you actually use your commands in your controllers, but you
probably have bigger issues to worry about then). 

There are no benchmarks for now, but proxifying your command dependencies is probably cheaper than actually acquiring
external resources that won't be used.

## Todo

- Add configuration settings to "lazify" commands on an on-demand basis
- Proxify dependencies used in setter injections
- Add LICENCE (which is MIT in case you're wondering)
- Add tests
- Add CI (Travis, Scrutinizer)
- Add badges
- Add library to packagist
- Create first release



