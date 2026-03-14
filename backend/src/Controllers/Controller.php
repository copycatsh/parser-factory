<?php

namespace App\Controllers;

use App\config\Database;
use Twig;

/**
 * Class Controller
 * @package App\Controllers
 */
abstract class Controller
{
    /**@var Database $query*/
    protected $query;
    
    protected $twig;
    
    public function __construct()
    {
        $loader = new Twig\Loader\FilesystemLoader(__DIR__ . '/../Views');
        $this->twig = new Twig\Environment($loader, []);
        $this->query = Database::getInstance()->dbConn();
    }
}