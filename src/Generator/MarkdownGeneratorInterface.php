<?php

namespace SoureCode\ConventionalChangelog\Generator;

interface MarkdownGeneratorInterface
{
    public function link(string $title, string $link): string;

    public function header(string $title, int $depth = 1): string;

    public function listing(array $items): string;

    public function newline(int $amount = 1): string;
}
