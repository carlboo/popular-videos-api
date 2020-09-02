<?php

namespace App\Models;

class PopularVideos
{
    protected $videosResource;
    protected $countryResource;

    public function __construct(CountryResource $countryResource, VideoResource $videosResource)
    {
        $this->countryResource = $countryResource;
        $this->videosResource = $videosResource;
    }

    public function getPopularPerCountry(array $countryList)
    {
        return collect($countryList)
            ->map(function($countryCode) {
                return [
                    'country' => [
                        'code' => $countryCode,
                        'name' => CountryResource::COUNTRIES[$countryCode],
                        'description' => $this->countryResource->getCountryInfo($countryCode),
                    ],
                    'items' => $this->videosResource->getVideos($countryCode),
                ];
            });
    }
}