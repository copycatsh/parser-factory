<?php

declare(strict_types=1);

namespace App\Parser;

use InvalidArgumentException;

final class ParserFactory
{
    public static function create(string $type, array $config = []): ParserInterface
    {
        return match ($type) {
            'hackernews' => new HackerNewsParser(),
            'rss' => new RssParser($config['url'] ?? throw new InvalidArgumentException('RSS parser requires "url" in config')),
            default => throw new InvalidArgumentException("Unknown parser type: {$type}"),
        };
    }
}
