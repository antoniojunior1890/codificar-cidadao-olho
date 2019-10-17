<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deputado;
use App\Models\VerbasIndenizatorias;
use GuzzleHttp\Pool;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;

class TesteController extends Controller
{
    public function update()
    {
        VerbasIndenizatorias::query()->truncate();

        $client = new Client();

        $requests = function ($total) {
            $uri = 'https://swapi.co/api/people/';
            for ($i = 0; $i < $total; $i++) {
                yield new Request('GET', $uri.$i);
            }
        };

        $pool = new Pool($client, $requests(20), [
            'concurrency' => 5,
            'fulfilled' => function ($response, $index) {
                $body = $response->getBody();
                $stringBody = (string) $body;
                $p = json_decode($stringBody, true);
                dd($p);
                VerbasIndenizatorias::create([
                    'deputado_id' => $p["height"],
                    'dataReferencia' => $p["name"],
                ]);
            },
            'rejected' => function ($reason, $index) {
                // this is delivered each failed request
            },
        ]);

        $promise = $pool->promise();

        $promise->wait();
    }
}
