<?php

namespace App\Repositories;

use GuzzleHttp\Client;

/**
 * Class ProjectsRepository
 * @package App\Repositories
 */
class ProjectsRepository {
    
    /**
     * @return mixed
     */
    public function exchangeList()
    {
        $client = new Client(['base_uri' => 'https://api.privatbank.ua']);
        
        $response = $client->request(
            'GET',
            'p24api/pubinfo?json&exchange&coursid=5'
        );
        
        if ($response->getBody()) {
            $response = $response->getBody()->getContents();
        }
        
        return json_decode($response, true);
    }
    
    /**
     * @param        $amount
     * @param string $fromCurrent
     * @param string $toCurrency
     * @return array
     */
    public function exchange($amount, $fromCurrent='RUR', $toCurrency = 'UAH'): array
    {
        $exchangeMapping = $this->exchangeList();
        $response = [];
        foreach ($exchangeMapping as $k => $currency) {
            if ($currency['ccy'] === $fromCurrent) {
                $response = [
                  'currency' => $toCurrency,
                  'amount' => $currency['buy'] * $amount,
                ];
            }
        }
        return $response;
    }
}
