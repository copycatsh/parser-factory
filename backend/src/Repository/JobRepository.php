<?php

declare(strict_types=1);

namespace App\Repository;

use PDO;

final class JobRepository
{
    public function __construct(
        private readonly PDO $pdo,
    ) {}

    public function saveMany(array $jobs, string $source): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO jobs (hn_id, source, author, title, text, url, posted_at)
             VALUES (:hn_id, :source, :author, :title, :text, :url, :posted_at)
             ON CONFLICT (hn_id) DO NOTHING'
        );

        $count = 0;
        foreach ($jobs as $job) {
            $postedAt = isset($job['time']) && $job['time'] > 0
                ? date('Y-m-d H:i:s', (int) $job['time'])
                : ($job['pub_date'] ?? null);

            $stmt->execute([
                'hn_id' => $job['hn_id'] ?? $job['link'] ?? uniqid($source . '_'),
                'source' => $source,
                'author' => $job['author'] ?? null,
                'title' => $job['title'] ?? null,
                'text' => $job['text'] ?? $job['description'] ?? null,
                'url' => $job['url'] ?? $job['link'] ?? null,
                'posted_at' => $postedAt,
            ]);

            $count += $stmt->rowCount();
        }

        return $count;
    }

    public function findAll(int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM jobs ORDER BY created_at DESC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function findBySource(string $source, int $limit = 50, int $offset = 0): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT * FROM jobs WHERE source = :source ORDER BY created_at DESC LIMIT :limit OFFSET :offset'
        );
        $stmt->bindValue('source', $source);
        $stmt->bindValue('limit', $limit, PDO::PARAM_INT);
        $stmt->bindValue('offset', $offset, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    public function count(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM jobs')->fetchColumn();
    }
}
