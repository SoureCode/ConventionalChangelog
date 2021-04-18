<?php

namespace SoureCode\ConventionalChangelog\Command;

use SoureCode\ConventionalChangelog\Aggregator\BreakingChangeAggregator;
use SoureCode\ConventionalChangelog\Aggregator\EntryAggregator;
use SoureCode\ConventionalChangelog\Collector\CommitCollector;
use SoureCode\ConventionalChangelog\Generator\ChangelogGenerator;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

class GenerateCommand extends Command
{
    protected static $defaultName = 'generate';

    private ChangelogGenerator $generator;
    private CommitCollector $collector;
    private EntryAggregator $entryAggregator;
    private BreakingChangeAggregator $breakingChangeAggregator;

    public function __construct(
        ChangelogGenerator $generator,
        CommitCollector $collector,
        EntryAggregator $entryAggregator,
        BreakingChangeAggregator $breakingChangeAggregator
    ) {
        parent::__construct();

        $this->generator = $generator;
        $this->collector = $collector;
        $this->entryAggregator = $entryAggregator;
        $this->breakingChangeAggregator = $breakingChangeAggregator;
    }


    protected function configure()
    {
        $this->setDescription('Generates a changelog based on commits')
            ->addArgument('from', InputArgument::REQUIRED, 'The begin commit')
            ->addArgument('to', InputArgument::REQUIRED, 'The end commit')
            ->setHelp(
                <<<HELP
Examples:
    Use it as argument:
    conventional-commits generate c0aa24899b15aaf6ad8fb76ead8f7092a79a1516 c0aa24899b15aaf6ad8fb76ead8f7092a79a1516
HELP
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        /**
         * @var string $from
         */
        $from = $input->getArgument('from');
        /**
         * @var string $to
         */
        $to = $input->getArgument('to');

        /*
         * Collect
         */
        $commits = $this->collector->collect($from, $to);

        /*
         * Aggregate
         */
        $groupedEntries = $this->entryAggregator->aggregate($commits);
        $breakingChanges = $this->breakingChangeAggregator->aggregate($groupedEntries);

        /*
         * Accumulate
         */
        // @todo Get PR by commit id and add to entry

        /*
         * Generate
         */
        $result = $this->generator->generate($groupedEntries, $breakingChanges);

        $output->write($result);

        return Command::SUCCESS;
    }

}
