<?php

namespace App\Http\Controllers;

use App\Exceptions\InProgressException;
use App\Models\CountryResource;
use App\Models\PopularVideos;
use Illuminate\Contracts\Queue\Queue;
use Illuminate\Http\Request;

class PopularVideosController extends Controller
{
    public function getVideosPerCountry(Request $request, PopularVideos $popularVideos)
    {
        $input = $this->validate($request, [
            'offset' => 'integer|min:0',
            'size' => 'integer|min:0',
        ]);

        $countries = CountryResource::getCodeList(
            $input['offset'] ?? null, 
            $input['size'] ?? null
        );
        return [
            'data' => $popularVideos->getPopularPerCountry($countries)
        ];
    }
}
