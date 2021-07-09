<?php

namespace App\Services;

use GuzzleHttp\Client;

/**
 * N1ParserService class
 */
class N1ParserService
{
    public function getPage()
    {
        $client = new Client();
        $respone = $client->get('https://novosibirsk.n1.ru/search/?rubric=flats&deal_type=sell&price_max=6000000&sort=-date&metro=2353444%2C2353445%2C2353446%2C2353447%2C2353449%2C2353450%2C2353451%2C2353452%2C2353453&material_type=monolith_brick%2Cbrick&total_area_min=42&floors_count_min=9');
        file_put_contents('index.html', $respone->getBody()->getContents());
    }
}
