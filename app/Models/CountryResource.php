<?php

namespace App\Models;

use App\Exceptions\InProgressException;
use App\Jobs\ExtractCountryInfoJob;
use Illuminate\Support\Facades\Cache;

class CountryResource
{
    public const TIME_CACHED = 24*60*60;   // 1 day
    public const CACHE_PREFIX = 'country_info';
    public const COUNTRIES = [
        'gb' => 'United Kingdom',
        'nl' => 'Netherlands',
        'de' => 'Germany',
        'fr' => 'France',
        'es' => 'Spain',
        'it' => 'Italy',
        'gr' => 'Greece',
    ];

    public static function getCodeList($offset, $lenght)
    {
        return array_slice(array_keys(self::COUNTRIES), $offset, $lenght);
    }

    public function getCountryInfo($countryCodes)
    {
        foreach ($countryCodes as $countryCode) {
            $refresh = [];
            if (!Cache::has(self::getCacheTag($countryCode))) {
                $refresh[] = $countryCode; 
            }
        }
        if (!empty($refresh)) {
            dispatch(new ExtractCountryInfoJob($refresh));
            return false;
        }

        return collect($countryCodes)
            ->mapWithKeys(function($code){
                return [
                    $code => Cache::get(self::getCacheTag($code))
                ];
            });
    }
    
    public static function getCacheTag(string $countryCode)
    {
        return self::CACHE_PREFIX . '.' . $countryCode;
    }
}