<?php

namespace SoureCode\ConventionalChangelog\Generator;

use SoureCode\ConventionalChangelog\Model\Entry;
use SoureCode\ConventionalChangelog\Writer\MarkdownWriterInterface;
use function count;

class ChangelogGenerator
{
    private MarkdownGeneratorInterface $generator;

    private MarkdownWriterInterface $writer;

    public function __construct(MarkdownGeneratorInterface $generator, MarkdownWriterInterface $writer)
    {
        $this->generator = $generator;
        $this->writer = $writer;
    }

    /**
     * @param Array<string, Entry[]> $entries
     * @param Entry[]                $breakingChanges
     */
    public function generate(array $entries, array $breakingChanges): string
    {
        $this->writer->clear();

        $index = 0;
        $length = count($entries);
        foreach ($entries as $group => $items) {
            $listItems = $this->generateListing($items);

            $this->group($group, $listItems);

            if ($index !== $length - 1) {
                $this->writer->newline(2);
            }

            ++$index;
        }

        if (count($breakingChanges) > 0) {
            $this->writer->newline(2);
            $breakingChangeListing = $this->generateBreakingChangeListing($breakingChanges);

            $this->group('BREAKING CHANGE', $breakingChangeListing);
        }

        return $this->writer->getOutput();
    }

    /**
     * @param Entry[] $items
     *
     * @return string[]
     */
    private function generateListing(array $items): array
    {
        $listItems = [];

        foreach ($items as $item) {
            $commit = $item->getCommit();
            $message = $item->getMessage();
            $header = $message->getHeader();

            $description = $header->getDescription();
            $hash = $commit->getHash();
            $shortHash = substr($hash, 0, 7);

            // @todo add pr

            $commitLink = $this->generator->link(
                $shortHash,
                'https://github.com/SoureCode/ConventionalCommits/commit/'.$hash
            );

            $listItems[] = sprintf('%s %s', $description, $commitLink);
        }

        return $listItems;
    }

    /**
     * @param string[] $items
     */
    private function group(string $group, array $items): void
    {
        $this->writer->header($group);
        $this->writer->newline(2);
        $this->writer->listing($items);
    }

    /**
     * @param Entry[] $breakingChanges
     *
     * @return string[]
     */
    private function generateBreakingChangeListing(array $breakingChanges): array
    {
        $listItems = [];

        foreach ($breakingChanges as $entry) {
            $message = $entry->getMessage();
            $body = $message->getBody();

            if ($body && '' !== $body) {
                $listItems[] = $body;
            }

            $footers = $message->getFooters();

            foreach ($footers as $footer) {
                if ('BREAKING CHANGE' === $footer->getKey()) {
                    $listItems[] = $footer->getValue();
                }
            }
        }

        return $listItems;
    }
}
