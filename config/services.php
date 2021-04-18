<?php

use SoureCode\ConventionalChangelog\Aggregator\BreakingChangeAggregator;
use SoureCode\ConventionalChangelog\Aggregator\EntryAggregator;
use SoureCode\ConventionalChangelog\Application;
use SoureCode\ConventionalChangelog\Collector\CommitCollector;
use SoureCode\ConventionalChangelog\Generator\ChangelogGenerator;
use SoureCode\ConventionalChangelog\Generator\MarkdownGenerator;
use SoureCode\ConventionalChangelog\Generator\MarkdownGeneratorInterface;
use SoureCode\ConventionalChangelog\Writer\MarkdownWriter;
use SoureCode\ConventionalChangelog\Writer\MarkdownWriterInterface;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;
use Symplify\GitWrapper\GitWrapper;
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
        ->load('SoureCode\\ConventionalChangelog\\Command\\', '../src/Command/*')
        ->lazy()
        ->tag('console.command');

    $services
        ->set(MarkdownGeneratorInterface::class, MarkdownGenerator::class)
        ->lazy();

    $services
        ->set(MarkdownWriterInterface::class, MarkdownWriter::class)
        ->args(
            [
                service(MarkdownGeneratorInterface::class),
            ]
        )
        ->lazy();

    $services
        ->set(ChangelogGenerator::class)
        ->args(
            [
                service(MarkdownGeneratorInterface::class),
                service(MarkdownWriterInterface::class),
            ]
        )
        ->lazy();

    $services
        ->set(GitWrapper::class)
        ->lazy();

    $services
        ->set(CommitCollector::class)
        ->args(
            [
                service(GitWrapper::class),
            ]
        )
        ->lazy();

    $services
        ->set(EntryAggregator::class)
        ->lazy();

    $services
        ->set(BreakingChangeAggregator::class)
        ->lazy();
};
