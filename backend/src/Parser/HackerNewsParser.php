<?php

declare(strict_types=1);

namespace App\Parser;

use GuzzleHttp\Client;

final class HackerNewsParser implements ParserInterface
{
    private const int MAX_COMMENTS = 100;

    private readonly Client $client;
    private readonly string $baseUrl;
    private readonly string $searchUrl;

    public function __construct()
    {
        $this->client = new Client(['timeout' => 10]);
        $this->baseUrl = getenv('HN_API_BASE_URL') ?: 'https://hacker-news.firebaseio.com/v0';
        $this->searchUrl = getenv('HN_SEARCH_URL') ?: 'https://hn.algolia.com/api/v1';
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

            $text = strip_tags(html_entity_decode($comment['text'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8'));
            $title = mb_strimwidth(strtok($text, "\n|"), 0, 80, '');

            $jobs[] = [
                'hn_id' => (string) $comment['id'],
                'author' => $comment['by'] ?? '',
                'title' => $title,
                'text' => mb_strimwidth($text, 0, 500, '…'),
                'time' => $comment['time'] ?? 0,
                'url' => "https://news.ycombinator.com/item?id={$comment['id']}",
            ];
        }

        return $jobs;
    }

    private function findHiringThread(): ?int
    {
        $response = $this->client->get($this->searchUrl . '/search', [
            'query' => [
                'query' => 'Ask HN Who is hiring',
                'tags' => 'ask_hn',
                'hitsPerPage' => 5,
                'numericFilters' => 'created_at_i>1700000000',
            ],
        ]);

        $data = json_decode($response->getBody()->getContents(), true);
        $hits = $data['hits'] ?? [];

        if (empty($hits)) {
            return null;
        }

        usort($hits, fn ($a, $b) => ($b['created_at_i'] ?? 0) <=> ($a['created_at_i'] ?? 0));

        return (int) $hits[0]['objectID'];
    }

    /** @return array<string, mixed> */
    private function fetchItem(int $id): array
    {
        $response = $this->client->get($this->baseUrl . "/item/{$id}.json");

        return json_decode($response->getBody()->getContents(), true) ?? [];
    }
}
