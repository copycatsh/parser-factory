<?php

declare(strict_types=1);

namespace App\Parser;

use GuzzleHttp\Client;

final class HackerNewsParser implements ParserInterface
{
    private const string BASE_URL = 'https://hacker-news.firebaseio.com/v0';
    private const int MAX_COMMENTS = 100;

    private readonly Client $client;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 10]);
    }

    public function parse(): array
    {
        $threadId = $this->findHiringThread();

        if ($threadId === null) {
            return [];
        }

        $thread = $this->fetchItem($threadId);
        $kids = $thread['kids'] ?? [];
        $kids = array_slice($kids, 0, self::MAX_COMMENTS);

        $jobs = [];
        foreach ($kids as $commentId) {
            $comment = $this->fetchItem($commentId);

            if (empty($comment) || ($comment['deleted'] ?? false) || ($comment['dead'] ?? false)) {
                continue;
            }

            $jobs[] = [
                'hn_id' => (string) $comment['id'],
                'author' => $comment['by'] ?? '',
                'text' => $comment['text'] ?? '',
                'time' => $comment['time'] ?? 0,
                'url' => "https://news.ycombinator.com/item?id={$comment['id']}",
            ];
        }

        return $jobs;
    }

    private function findHiringThread(): ?int
    {
        $response = $this->client->get('https://hn.algolia.com/api/v1/search', [
            'query' => [
                'query' => 'Ask HN Who is hiring',
                'tags' => 'ask_hn',
                'hitsPerPage' => 1,
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        $hits = $data['hits'] ?? [];

        if (empty($hits)) {
            return null;
        }

        return (int) $hits[0]['objectID'];
    }

    private function fetchItem(int $id): array
    {
        $response = $this->client->get(self::BASE_URL . "/item/{$id}.json");

        return json_decode($response->getBody()->getContents(), true) ?? [];
    }
}
