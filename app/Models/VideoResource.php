<?php

namespace App\Models;


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

    public function getVideos($countryCodes)
    {
        $dataAvailable = true;
        foreach ($countryCodes as $countryCode) {
            if (!Cache::has(self::getCacheTag($countryCode))) {
                $this->refreshCache($countryCode);
                $dataAvailable = false;
            }
        }
        if ($dataAvailable === false) {
            return false;
        }

        return
            collect($countryCodes)->mapWithKeys(function($countryCode) {
                return [
                    $countryCode => Cache::get(self::getCacheTag($countryCode))
                ];
            });
    }

    protected function refreshCache($countryCode)
    {
        Cache::tags(self::getCacheTag($countryCode))->flush();
        dispatch(new ExtractVideoDataJob($countryCode, 'none'));
    }
    
}