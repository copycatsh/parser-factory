<?php

declare(strict_types=1);

require_once __DIR__ . '/../vendor/autoload.php';

use App\Controller\ApiController;
use App\Database\Connection;
use App\Http\Request;
use App\Http\Response;
use App\Repository\JobRepository;

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}

$request = new Request();
$uri = strtok($request->getUri(), '?');
$method = $request->getMethod();

$pdo = Connection::get();
$jobRepository = new JobRepository($pdo);
$controller = new ApiController($jobRepository);

match (true) {
    $method === 'GET' && $uri === '/api/health' => Response::json(['status' => 'ok']),
    $method === 'GET' && $uri === '/api/jobs' => $controller->getJobs($request),
    $method === 'POST' && $uri === '/api/parse' => $controller->parse($request),
    default => Response::json(['error' => 'Not Found'], 404),
};
