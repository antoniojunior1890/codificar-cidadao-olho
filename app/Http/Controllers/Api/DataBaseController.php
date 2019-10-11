<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deputado;
use Illuminate\Http\Request;
use GuzzleHttp\Client as Guzzle;

class DataBaseController extends Controller
{
    public function update()
    {

        $guzzle = new Guzzle;
        $response = $guzzle->request('GET','http://dadosabertos.almg.gov.br/ws/deputados/em_exercicio',[
            'query' => ['formato' => 'json']
        ]);
        $jsonObj = json_decode($response->getBody(), true);
        $deputados = $jsonObj["list"];

        foreach ( $deputados as $deputado )
        {
            Deputado::create([
                'id'        => $deputado["id"],
                'nome'      => $deputado["nome"],
                'partido'   => $deputado["partido"],
                'tagLocalizacao' => $deputado["tagLocalizacao"],
             ]);
        }

    }
}
