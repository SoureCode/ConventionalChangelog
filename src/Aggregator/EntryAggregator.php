<?php

namespace SoureCode\ConventionalChangelog\Aggregator;

use SoureCode\ConventionalChangelog\Model\Entry;
use SoureCode\ConventionalCommits\Message\Message;
use Symplify\GitWrapper\GitCommit;

class EntryAggregator
{

    /**
     * @param GitCommit[] $commits
     *
     * @return Array<string, Entry[]>
     */
    public function aggregate(array $commits): array
    {
        $items = [];

        foreach ($commits as $commit) {
            $message = Message::fromString(sprintf("%s\n%s", $commit->getSubject(), $commit->getBody()));
            $header = $message->getHeader();

            $entry = new Entry($commit, $message);

            $items[$header->getType()][] = $entry;
        }

        ksort($items, SORT_STRING);

        return $items;
    }

}
