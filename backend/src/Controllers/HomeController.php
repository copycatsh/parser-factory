<?php

namespace App\Controllers;

use App\Repositories\ProjectsRepository;
use GuzzleHttp\Client;
use Simplon\Mysql\MysqlException;
use Twig\Error\LoaderError;
use Twig\Error\RuntimeError;
use Twig\Error\SyntaxError;

/**
 * Class HomeController
 * @package App\Controllers
 */
class HomeController extends Controller
{
    /**@var ProjectsRepository $projectRepository*/
    private $projectRepository;
    
    public function __construct(ProjectsRepository $projectRepository)
    {
        parent::__construct();
        $this->projectRepository = $projectRepository;
    }
    
    /**
     * @return array|false|string|null
     * @throws MysqlException
     * @throws LoaderError
     * @throws RuntimeError
     * @throws SyntaxError
     */
    public function index()
    {
        $projects = $this->query->fetchRowMany('SELECT p.* FROM projects as p');
        
        if ($projects === null) {
            return $this->twig->render('index.twig', [
                'projects' => null,
                'dataPoints' => []
            ]);
        }
    
        $res = [];
        $pieData = [
            'none' => 0,
            'under500' => 0,
            '500-1000' => 0,
            '1000-5000' => 0,
            'above5000' => 0,
        ];
        
        foreach ($projects as $key => $project) {
            $currentEmployer = json_decode($project['employer'], true);
            $budget = json_decode($project['budget'], true);
    
            $exchange = $budget ? $this->projectRepository->exchange($budget['amount']): null;
    
            $this->pieDataRank($pieData, $exchange);
            $res[] = [
                'name' => $project['name'],
                'site_project' => $project['site_project'],
                'budget' => $budget ?  $exchange['amount']: null,
                'login_employer' => $currentEmployer['login'],
                'first_name' => $currentEmployer['first_name'],
                'last_name' => $currentEmployer['last_name'],
            ];
        }
        $count = count($projects);
        $dataPoints = [];
        if ($count > 0) {
            $dataPoints = [
                'Not specified' =>$pieData['none']/$count * 100,
                'under 500 UAH' =>$pieData['under500']/$count * 100,
                '500-1000 UAH' => $pieData['500-1000']/$count * 100,
                '1000-5000 UAH' => $pieData['1000-5000']/$count * 100,
                '5000+ UAH' => $pieData['above5000']/$count * 100
            ];
        }
       
        $dataPoints = $dataPoints ? json_encode($dataPoints, JSON_NUMERIC_CHECK) : null;
        
        return $this->twig->render('index.twig', [
            'projects' => $res,
            'dataPoints' => $dataPoints
        ]);
    }
    
    /**
     * @param array $data
     * @param null  $amount
     * @return array
     */
    private function pieDataRank(array &$data, $amount = null)
    {
        if ($amount === null) {
            ++$data['none'];
        } elseif ($amount['amount'] < 500) {
            ++$data['under500'];
        } elseif ($amount['amount'] > 500 && $amount['amount'] < 1000) {
            ++$data['500-1000'];
        } elseif ($amount['amount'] > 1000 && $amount['amount'] < 5000) {
            ++$data['1000-5000'];
        } elseif ($amount['amount'] > 5000) {
            ++$data['above5000'];
        }
        return $data;
    }
    
    /**
     * @return bool|int
     * @throws MysqlException
     */
    public function insert()
    {
        $data = [
            'id'   => false,
            'name' => 'Peter',
            'description' => 'Parker',
        ];
    
        return $this->query->insert('projects', $data);
    }
    
    /**
     *
     */
    public function test()
    {
        echo 'Hello TEST action from HomeController!!!';
    }
    
    /**
     * @return false|string
     * @throws MysqlException
     */
    public function getList()
    {
        $client = new Client(['base_uri' => 'https://api.freelancehunt.com']);
        
        $response = $client->request(
            'GET',
            'v2/projects',
            ['headers' =>
                [
                    'Authorization' => 'Bearer '.getenv('MY_APP_TOKEN')
                ]
            ]
        );
        
        if ($response->getBody()) {
            $response = $response->getBody()->getContents();
        }
        
        $result = \GuzzleHttp\json_decode($response, true);
        $data = [];
        foreach ($result['data'] as $key => $values) {
            $coding[] = [
                mb_detect_encoding($values['attributes']['name']),
                mb_detect_encoding($values['attributes']['description']),
            ];
            $data[] = [
                'name' => $values['attributes']['name'],
                'description' => mb_strimwidth($values['attributes']['description'], 0, 150, '...'),
                'skills' => $values['attributes']['skills'] ? json_encode($values['attributes']['skills'], JSON_UNESCAPED_UNICODE) : '{}',
                'budget' => $values['attributes']['budget'] ? json_encode($values['attributes']['budget'], JSON_UNESCAPED_UNICODE): '{}',
                'site_project' => $values['links']['self']['web'],
                'employer' => $values['attributes']['employer'] ? json_encode($values['attributes']['employer'], JSON_UNESCAPED_UNICODE): '{}',
                'projectId' => $values['id'],
            ];
        }
        $this->query->executeSql('TRUNCATE projects');
    
        $records = $this->query->insertMany('projects', $data);
    
        return json_encode( ['status' => 'created','records' => $records], JSON_UNESCAPED_UNICODE);
    }
}
