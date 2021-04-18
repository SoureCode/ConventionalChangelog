<?php

namespace SoureCode\ConventionalChangelog\Model;

use SoureCode\ConventionalCommits\Message\Message;
use Symplify\GitWrapper\GitCommit;

class Entry
{

    private GitCommit $commit;

    private Message $message;

    public function __construct(GitCommit $commit, Message $message)
    {
        $this->commit = $commit;
        $this->message = $message;
    }

    public function getCommit(): GitCommit
    {
        return $this->commit;
    }

    public function getMessage(): Message
    {
        return $this->message;
    }

}
