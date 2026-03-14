<?php

declare(strict_types=1);

namespace App\Controller;

use App\Http\Request;
use App\Http\Response;
use App\Parser\ParserFactory;
use App\Repository\JobRepository;

final class ApiController
{
    public function __construct(
        private readonly JobRepository $jobRepository,
    ) {
    }

    public function getJobs(Request $request): void
    {
        $params = $_GET;
        $limit = max(1, min(100, (int) ($params['limit'] ?? 50)));
        $offset = max(0, (int) ($params['offset'] ?? 0));
        $source = $params['source'] ?? null;

        $data = $source !== null
            ? $this->jobRepository->findBySource($source, $limit, $offset)
            : $this->jobRepository->findAll($limit, $offset);

        Response::json([
            'data' => $data,
            'total' => $this->jobRepository->count(),
            'limit' => $limit,
            'offset' => $offset,
        ]);
    }

    public function parse(Request $request): void
    {
        $body = $request->getBody();
        $source = $body['source'] ?? null;

        if ($source === null) {
            Response::json(['error' => 'Missing "source" field'], 400);
            return;
        }

        $config = [];
        if (isset($body['url'])) {
            $config['url'] = $body['url'];
        }

        $parser = ParserFactory::create($source, $config);
        $results = $parser->parse();
        $saved = $this->jobRepository->saveMany($results, $source);

        Response::json([
            'status' => 'ok',
            'parsed' => $saved,
            'source' => $source,
        ]);
    }
}
