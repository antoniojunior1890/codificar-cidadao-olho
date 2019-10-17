<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Ixudra\Curl\Facades\Curl;
use App\Models\Deputado;

use GuzzleHttp\Client as Guzzle;

class DeputadoController extends Controller
{
    public function update()
    {
        $guzzle = new Guzzle;
        $response = $guzzle->request('GET','http://dadosabertos.almg.gov.br/ws/deputados/em_exercicio',[
            'query' => ['formato' => 'json'],
            'headers' => [
                'User-Agent' => 'testing/1.0',
            ]
//            'debug' => true,
        ]);
        $jsonObj = json_decode($response->getBody(), true);
        $deputados = $jsonObj["list"];

        DB::beginTransaction();

        try{
            Deputado::query()->truncate();

            foreach ($deputados as $deputado) {
                Deputado::create([
                    'id' => $deputado["id"],
                    'nome' => $deputado["nome"],
                    'partido' => $deputado["partido"],
                    'tagLocalizacao' => $deputado["tagLocalizacao"],
                ]);
            }

            DB::commit();

            return [
                'success'   => true,
                'message'   =>'Dados atualizados com sucesso'
            ];

        }catch (\Exception $e) {
            DB::rollback();
            return [
                'success'   => false,
                'message'   =>'Falha ao atualizar dados : '.$e
            ];
        }

    }
}
