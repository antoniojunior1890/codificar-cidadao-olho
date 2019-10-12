<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deputado;
use Illuminate\Support\Facades\DB;

use Ixudra\Curl\Facades\Curl;

class DataBaseController extends Controller
{
    public function update()
    {

        $response = Curl::to('http://dadosabertos.almg.gov.br/ws/deputados/em_exercicio?formato=json')
            ->get();

        $jsonObj = json_decode($response, true);

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
