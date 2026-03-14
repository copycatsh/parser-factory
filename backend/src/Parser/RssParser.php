<?php

declare(strict_types=1);

namespace App\Parser;

use GuzzleHttp\Client;

final class RssParser implements ParserInterface
{
    private readonly Client $client;

    public function __construct(
        private readonly string $feedUrl,
    ) {
        $this->client = new Client(['timeout' => 10]);
    }

    public function parse(): array
    {
        $response = $this->client->get($this->feedUrl);
        $xml = simplexml_load_string($response->getBody()->getContents());

        if ($xml === false) {
            return [];
        }

        $items = [];
        foreach ($xml->channel->item as $item) {
            $items[] = [
                'title' => (string) $item->title,
                'description' => (string) $item->description,
                'link' => (string) $item->link,
                'pub_date' => (string) $item->pubDate,
            ];
        }

        return $items;
    }
}
