<?php

namespace SoureCode\ConventionalChangelog\Collector;

use Symplify\GitWrapper\GitCommit;
use Symplify\GitWrapper\GitWrapper;

class CommitCollector
{

    private GitWrapper $gitWrapper;

    public function __construct(GitWrapper $gitWrapper)
    {
        $this->gitWrapper = $gitWrapper;
    }

    /**
     * @param string $from
     * @param string $to
     *
     * @return GitCommit[]
     */
    public function collect(string $from, string $to): array
    {
        $gitWorkingCopy = $this->gitWrapper->workingCopy(getcwd());
        $gitCommits = $gitWorkingCopy->commits();

        $commitHashes = $gitCommits->fetchRange($from, $to);

        $commits = array_map(fn (string $hash) => $gitCommits->get($hash), $commitHashes);

        return $commits;
    }

}
