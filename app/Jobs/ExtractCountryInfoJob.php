<?php

namespace App\Jobs;

use App\Models\CountryResource;
use GuzzleHttp\Client;
use GuzzleHttp\Promise;
use Illuminate\Support\Facades\Cache;

class ExtractCountryInfoJob extends Job
{
    protected $countryCodes;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($countryCodes)
    {
        
        $this->countryCodes = $countryCodes;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        

        $client = new Client(['base_uri' => env('WIKIPEDIA_API_EXTRACT_URL')]);

        $promises = collect($this->countryCodes)
            ->mapWithKeys(function($code) use ($client) {
                return [
                    $code => $client->getAsync('', [
                        'query' => [
                            'action' => 'query',
                            'prop' => 'extracts',
                            'exintro' => 1,
                            'titles' => CountryResource::COUNTRIES[$code],
                            'explaintext' => 1,
                            'format' => 'json',
                        ],
                        'headers' => ['Accept' => 'application/json']
                    ])
                ];
            });

        // Wait for the requests to complete; throws a ConnectException
        // if any of the requests fail
        $responses = Promise\unwrap($promises);

        foreach ($responses as $key => $response) {
            $body = json_decode($response->getBody()->getContents(), true);
                $data = reset($body['query']['pages'])['extract'];
                Cache::put(
                    CountryResource::getCacheTag($key),
                    $data,
                    CountryResource::TIME_CACHED
                );
        }
    }
}
