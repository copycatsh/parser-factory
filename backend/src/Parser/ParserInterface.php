<?php

declare(strict_types=1);

namespace App\Parser;

interface ParserInterface
{
    /**
     * @return array<int, array<string, mixed>>
     */
    public function parse(): array;
}
