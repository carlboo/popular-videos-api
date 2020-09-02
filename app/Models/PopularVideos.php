<?php

namespace App\Models;

use App\Exceptions\InProgressException;

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
        $videos = $this->videosResource->getVideos($countryList);
        $descriptions = $this->countryResource->getCountryInfo($countryList);

        if ($videos === false || $descriptions === false) {
            throw new InProgressException();
        }

        return collect($countryList)
            ->map(function ($countryCode) use ($videos, $descriptions) {
                return [
                    'country' => [
                        'code' => $countryCode,
                        'name' => CountryResource::COUNTRIES[$countryCode],
                        'description' => $descriptions[$countryCode]
                    ],
                    'items' => $videos[$countryCode]
                ];
            });
    }
}