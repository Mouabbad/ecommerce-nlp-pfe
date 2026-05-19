<?php

namespace App\Service;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class SentimentAnalyzer
{
    private HttpClientInterface $client;

    public function __construct(HttpClientInterface $client)
    {
        $this->client = $client;
    }

    /**
     * 
     *
     * @param string $text
     * @return array|null
     */
    public function analyzeSentiment(string $text): ?array
    {
        $response = $this->client->request(
            'POST',
            'http://127.0.0.1:8001/analyze',  // URL du API  qui ylilide le model d'NLP
            [
                'json' => ['text' => $text],
            ]
        );

        if ($response->getStatusCode() !== 200) {
            return null;  
        }

        return $response->toArray(); // exemple il donne ['sentiment' => 'positive', 'score' => 0.95] 
    }
}
