<?php

namespace SoureCode\ConventionalChangelog\Generator;

use function assert;

class MarkdownGenerator implements MarkdownGeneratorInterface
{
    public function link(string $title, string $link): string
    {
        return sprintf('[%s](%s)', $title, $link);
    }

    public function header(string $title, int $depth = 1): string
    {
        assert($depth >= 1, 'Depth musst be greater or equal to 1');
        assert($depth <= 6, 'Depth musst be less or equal to 6');

        return sprintf('%s %s', str_repeat('#', $depth), $title);
    }

    public function listing(array $items): string
    {
        $items = array_map(static fn (string $item): string => sprintf('- %s', $item), $items);

        return implode($this->newline(), $items);
    }

    public function newline(int $amount = 1): string
    {
        assert($amount >= 1, 'Amount musst be greater or equal to 1');

        return str_repeat("\n", $amount);
    }
}
