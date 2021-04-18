<?php

namespace SoureCode\ConventionalChangelog\Aggregator;

use SoureCode\ConventionalChangelog\Model\Entry;

class BreakingChangeAggregator
{
    /**
     * @param Array<string, Entry[]> $groupedEntries
     *
     * @return Entry[]
     */
    public function aggregate(array $groupedEntries): array
    {
        $items = [];

        foreach ($groupedEntries as $entries) {
            foreach ($entries as $entry) {
                if ($this->isBreakingChange($entry)) {
                    $items[] = $entry;
                }
            }
        }

        return $items;
    }

    private function isBreakingChange(Entry $entry): bool
    {
        $message = $entry->getMessage();
        $header = $message->getHeader();

        if ($header->isBreakingChange()) {
            return true;
        }

        $footers = $message->getFooters();

        foreach ($footers as $footer) {
            if ('BREAKING CHANGE' === $footer->getKey()) {
                return true;
            }
        }

        return false;
    }
}
