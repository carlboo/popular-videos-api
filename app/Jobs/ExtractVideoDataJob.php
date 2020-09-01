<?php

namespace App\Jobs;

use App\Models\VideoResource;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Cache;

class ExtractVideoDataJob extends Job
{
    protected const MAX_ENTRIES=20;
    protected $countryCode;
    protected $pageToken;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(string $countryCode, string $pageToken)
    {
        $this->countryCode = $countryCode;
        $this->pageToken = $pageToken;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $requestParameters = [
            'part' => 'snippet',
            'chart' => 'mostPopular',
            'regionCode' => $this->countryCode,
            'maxResults' => self::MAX_ENTRIES,
            'key' => env('GOOGLE_API_KEY'),
            'pageToken' => $this->pageToken != 'none' ? $this->pageToken : null,
        ];

        $url = env('GOOGLE_API_VIDEO_URL');
        $client = app(Client::class);
        $response = $client->request('GET', $url, [
            'query' => $requestParameters,
            'headers' => ['Accept' => 'application/json'],
        ]);

        if ($response->getStatusCode() === 200) {
            $body = json_decode($response->getBody()->getContents(), true);
            $data = collect($body['items'])
                ->mapWithKeys(function($item) {
                    return [
                        $item['id'] => [
                            'description' => $item['snippet']['description'] ?? null,
                            'thumbnail_normal' => $item['snippet']['thumbnails']['default'] ?? null,
                            'thumbnail_high' => $item['snippet']['thumbnails']['high'] ?? null,
                        ]
                    ];
                })
                ->all();

            Cache::put(
                VideoResource::getCacheTag($this->countryCode), 
                array_merge(Cache::get(VideoResource::getCacheTag($this->countryCode), []), $data),
                VideoResource::TIME_CACHED
            );

            if($nextPageToken = $body['nextPageToken'] ?? false) {
                dispatch(new ExtractVideoDataJob($this->countryCode, $nextPageToken));
            }
        }
    }
}
