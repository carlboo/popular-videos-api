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

    public function getCountryInfo($countryCode)
    {
        if (!Cache::has(self::getCacheTag($countryCode))) {
            $this->refreshCache();
            throw new InProgressException();
        }
        return Cache::get(self::getCacheTag($countryCode));
    }
    
    public static function getCacheTag(string $countryCode)
    {
        return self::CACHE_PREFIX . '.' . $countryCode;
    }

    protected function refreshCache()
    {
        foreach (array_keys(self::COUNTRIES) as $key) {
            Cache::tags(self::getCacheTag($key))->flush();
            dispatch(new ExtractCountryInfoJob($key));
        }
    }
}