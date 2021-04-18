<?php

namespace SoureCode\ConventionalChangelog\Writer;

interface MarkdownWriterInterface
{
    public function write(string $text): self;

    public function link(string $title, string $link): self;

    public function header(string $title, int $depth = 1): self;

    public function listing(array $items): self;

    public function newline(int $amount = 1): self;

    public function clear(): void;

    public function getOutput(): string;
}
