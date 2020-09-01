<?php

namespace App\Jobs;

use App\Jobs\Actions\ExtractCountryInfoAction;
use App\Models\CountryResource;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class ExtractCountryInfoJob extends Job
{
    protected $countryCode;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $countryCode)
    {
        $this->countryCode = $countryCode;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $url = env('WIKIPEDIA_API_EXTRACT_URL');
        $requestParameters = [
            'action' => 'query',
            'prop' => 'extracts',
            'exintro' => 1,
            'titles' => CountryResource::COUNTRIES[$this->countryCode],
            'explaintext' => 1,
            'format' => 'json',
        ];

        $client = app(Client::class);
        $response = $client->request('GET', $url, [
            'query' => $requestParameters,
            'headers' => ['Accept' => 'application/json'],
        ]);

        if ($response->getStatusCode() === 200) {
            $body = json_decode($response->getBody()->getContents(), true);
            $data = reset($body['query']['pages'])['extract'];
            Cache::put(
                CountryResource::getCacheTag($this->countryCode),
                $data,
                CountryResource::TIME_CACHED
            );
        }
    }
}
