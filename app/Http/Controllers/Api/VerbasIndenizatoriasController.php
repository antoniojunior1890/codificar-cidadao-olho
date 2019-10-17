<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deputado;
use App\Models\VerbasIndenizatorias;
use Illuminate\Support\Facades\DB;

use GuzzleHttp\Client as Guzzle;

class VerbasIndenizatoriasController extends Controller
{
    public function update()
    {
        $deputadosDB = Deputado::all();

        if(count($deputadosDB) == 0){
            return [
                'success'   => false,
                'message'   =>'Atualize dados deputados'
            ];
        }

        $guzzle = new Guzzle;

        DB::beginTransaction();

        try{
            VerbasIndenizatorias::query()->truncate();

            foreach ($deputadosDB as $deputado) {
                $response = $guzzle->request('GET','http://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/legislatura_atual/deputados/'.$deputado["id"].'/datas',[
                    'query' => ['formato' => 'json'],
                    'headers' => [
                        'User-Agent' => 'testing/1.0',
                    ],
                ]);

                $jsonObj = json_decode($response->getBody(), true);
                $verbasIdenizatorias = $jsonObj["list"];

                foreach ($verbasIdenizatorias as $verbasIdenizatoria) {
                    VerbasIndenizatorias::create([
                        'deputado_id' => $verbasIdenizatoria["idDeputado"],
                        'dataReferencia' => $verbasIdenizatoria["dataReferencia"]["$"],
                    ]);
                }
            }

            DB::commit();

            return [
                'success'   => true,
                'message'   =>'Dados verbas atualizados com sucesso'
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
