<?php

namespace App\Models;

class Countries
{
    public const TITLES = [
        'uk' => 'United Kingdom',
        'nl' => 'Netherlands',
        'de' => 'Germany',
        'fr' => 'France',
        'es' => 'Spain',
        'it' => 'Italy',
        'gr' => 'Greece',
    ];

    public function getList($offset, $lenght){
        return array_slice(self::TITLES, $offset, $lenght);
    }
}