<?php

ini_set('display_errors', E_ALL);
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../src/config/Database.php';
require_once __DIR__ . '/../src/Controllers/HomeController.php';
require_once __DIR__ . '/../src/Repositories/ProjectsRepository.php';

    if(file_exists(__DIR__ . '/../env.php')) {
        require_once __DIR__ . '/../env.php';
    }

use App\Controllers\HomeController;
use Klein\Klein;
use App\Repositories\ProjectsRepository;

$klein = new Klein();

$projectRepository = new ProjectsRepository();

$klein->respond('GET', '/?', function($request, $response)  use (&$projectRepository) {
    $controller = new HomeController($projectRepository);
    $response = $controller->index();
    return $response;
});

$klein->respond('GET', '/test', function ($request, $response) use (&$projectRepository){
    $controller = new HomeController($projectRepository);
    $response = $controller->test();
    return $response;
});

$klein->respond('GET', '/insert', function ($request, $response) use (&$projectRepository){
    $controller = new HomeController($projectRepository);
    $response = $controller->insert();
    return $response;
});


$klein->respond('GET', '/projects', function ($request, $response) use (&$projectRepository) {
    $controller = new HomeController($projectRepository);
    $response = $controller->getList();
    return $response;
});

$klein->dispatch();
