<?php

namespace SoureCode\ConventionalChangelog\Collector;

use Symplify\GitWrapper\GitCommit;
use Symplify\GitWrapper\GitWrapper;

class CommitCollector
{

    private GitWrapper $gitWrapper;

    private string $workingDirectory;

    public function __construct(GitWrapper $gitWrapper, string $workingDirectory)
    {
        $this->gitWrapper = $gitWrapper;
        $this->workingDirectory = $workingDirectory;
    }

    /**
     * @param string $from
     * @param string $to
     *
     * @return GitCommit[]
     */
    public function collect(string $from, string $to): array
    {
        $gitWorkingCopy = $this->gitWrapper->workingCopy($this->workingDirectory);
        $gitCommits = $gitWorkingCopy->commits();

        $commitHashes = $gitCommits->fetchRange($from, $to);

        $commits = array_map(fn (string $hash) => $gitCommits->get($hash), $commitHashes);

        return $commits;
    }

}
