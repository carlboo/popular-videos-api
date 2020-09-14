<?php

namespace App\Models;


use App\Jobs\ExtractVideoDataJob;
use Illuminate\Support\Facades\Cache;

class VideoResource
{
    public const TIME_CACHED = 30*60;   // 30 min
    public const CACHE_PREFIX = 'video';
    public const CACHE_SUFIX_TEMP = 'temp';
    
    public static function getCacheTag($countryCode, $temp = false)
    {
        return self::CACHE_PREFIX . ".$countryCode"
        . $temp ? ('.' . self::CACHE_SUFIX_TEMP) : '';
    }

    public function getVideos($countryCodes)
    {
        if (!$this->isInCacheOrRequest($countryCodes)) {
            return false;
        }

        return
            collect($countryCodes)->mapWithKeys(function($countryCode) {
                return [
                    $countryCode => Cache::get(self::getCacheTag($countryCode))
                ];
            })
            ->all();
    }

    protected function isInCacheOrRequest($countryCodes)
    {
        $dataAvailable = true;
        foreach ($countryCodes as $countryCode) {
            if (!Cache::has(self::getCacheTag($countryCode))) {
                $this->refreshCache($countryCode);
                $dataAvailable = false;
            }
        }
        return $dataAvailable;
    }

    protected function refreshCache($countryCode)
    {
        if (!Cache::has(self::getCacheTag($countryCode), true)) {
            dispatch(new ExtractVideoDataJob($countryCode, 'none'));
        }
    }
    
}