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
            ->mapWithKeys(function($countryCode) {
                return [
                    $countryCode => [
                        'country_info' => $this->countryResource->getCountryInfo($countryCode),
                        'items' => $this->videosResource->getVideos($countryCode),
                    ]
                ];
            });
    }
}