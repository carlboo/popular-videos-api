<?php

namespace App\Models;

use App\Exceptions\InProgressException;
use App\Jobs\ExtractVideoDataJob;
use Illuminate\Support\Facades\Cache;

class VideoResource
{
    public const TIME_CACHED = 30*60;   // 30 min
    public const CACHE_PREFIX = 'video';
    
    public static function getCacheTag($countryCode)
    {
        return self::CACHE_PREFIX . '.' . $countryCode;
    }

    public function getVideos($countryCode)
    {
        if (!Cache::has(self::getCacheTag($countryCode))) {
            $this->refreshCache();
            throw new InProgressException();
        }
        return Cache::get(self::getCacheTag($countryCode));
    }

    protected function refreshCache()
    {
        foreach (array_keys(CountryResource::COUNTRIES) as $key) {
            Cache::tags(self::getCacheTag($key))->flush();
            dispatch(new ExtractVideoDataJob($key, 'none'));
        }
    }
}