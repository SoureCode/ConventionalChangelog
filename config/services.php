<?php

use SoureCode\ConventionalChangelog\Application;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use function Symfony\Component\DependencyInjection\Loader\Configurator\service;

return function (ContainerConfigurator $configurator) {
    $services = $configurator->services();

    $services->defaults()
        ->autowire(true)
        ->autoconfigure(true);

    $services
        ->set(Application::class)
        ->lazy()
        ->call('setCommandLoader', [service('console.command_loader')])
        ->public();

    $services
        ->load('SoureCode\\ConventionalCommits\\Commands\\', '../src/Commands/*')
        ->lazy()
        ->tag('console.command');

};
