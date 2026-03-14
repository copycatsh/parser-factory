<?php

declare(strict_types=1);

namespace App\Http;

final class Request
{
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'] ?? 'GET';
    }

    public function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'] ?? '/';

        return parse_url($uri, PHP_URL_PATH) ?: '/';
    }

    /** @return array<string, mixed> */
    public function getBody(): array
    {
        $input = file_get_contents('php://input');

        return json_decode($input ?: '', true) ?? [];
    }
}
