<?php

namespace SoureCode\ConventionalChangelog\Writer;

use SoureCode\ConventionalChangelog\Generator\MarkdownGeneratorInterface;

class MarkdownWriter implements MarkdownWriterInterface
{
    private MarkdownGeneratorInterface $generator;

    private string $output = '';

    public function __construct(MarkdownGeneratorInterface $generator)
    {
        $this->generator = $generator;
    }

    public function write(string $text): self
    {
        $this->output .= $text;

        return $this;
    }

    public function link(string $title, string $link): self
    {
        $this->output .= $this->generator->link($title, $link);

        return $this;
    }

    public function header(string $title, int $depth = 1): self
    {
        $this->output .= $this->generator->header($title, $depth);

        return $this;
    }

    public function listing(array $items): self
    {
        $this->output .= $this->generator->listing($items);

        return $this;
    }

    public function newline(int $amount = 1): self
    {
        $this->output .= $this->generator->newline($amount);

        return $this;
    }

    public function clear(): void
    {
        $this->output = '';
    }

    public function getOutput(): string
    {
        return $this->output;
    }
}
