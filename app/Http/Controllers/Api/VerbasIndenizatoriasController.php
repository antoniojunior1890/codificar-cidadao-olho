<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Deputado;
use App\Models\VerbasIndenizatorias;
use Carbon\Carbon;
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

        $aux = [];

        for ($i = 0; $i < 10; $i++) {
            $aux[$i] = $deputadosDB[$i];
        }

        $guzzle = new Guzzle;

        DB::beginTransaction();

        try{
            VerbasIndenizatorias::query()->truncate();

            foreach ($deputadosDB as $deputado) {

                $dataVerbaDeputado = [];

                for ($i = 1; $i <= 12; $i++) {
                    list($usec, $sec) = explode(' ', microtime());
                    $script_start = (float) $sec + (float) $usec;

                    $response = $guzzle->request('GET', 'http://dadosabertos.almg.gov.br/ws/prestacao_contas/verbas_indenizatorias/legislatura_atual/deputados/'.$deputado["id"].'/2019/'.$i, [
                        'query' => ['formato' => 'json'],
                        'headers' => [
                            'User-Agent' => 'testing/1.0',
                        ],
                    ]);

                    $jsonObj = json_decode($response->getBody(), true);
                    $verbasIdenizatorias = $jsonObj["list"];

                    if(!empty($verbasIdenizatorias)) {
                        $dataVerbaDeputado[$i] = count($verbasIdenizatorias);
//                        dd($verbasIdenizatorias);
//                    foreach ($verbasIdenizatorias as $verbasIdenizatoria) {
//////                        dd($verbasIdenizatoria["listaDetalheVerba"]);
//                        $mes = Carbon::parse($verbasIdenizatoria["listaDetalheVerba"][0]["dataEmissao"]["$"])->format('m');
////                        dd(Carbon::parse($verbasIdenizatoria["listaDetalheVerba"][0]["dataEmissao"]["$"])->format('m'));
//                        $dataVerbaDeputado[$mes] += 1;
//////                        dd(Carbon::parse($verbasIdenizatoria["listaDetalheVerba"][0]["dataEmissao"]["$"])->format('m'));
//////                        dd(count($verbasIdenizatoria["listaDetalheVerba"]));
//////                        VerbasIndenizatorias::create([
//////                            'deputado_id' => $verbasIdenizatoria["idDeputado"],
//////                            'dataReferencia' => $verbasIdenizatoria["dataReferencia"]["$"],
//////                        ]);
//                    }

                    }
                }
//                    dd($dataVerbaDeputado);
                foreach ($dataVerbaDeputado as $key => $value) {
                    $date = Carbon::createFromFormat('Y-m-d','2019-'.$key.'-01');
//                    dd($date->toDateString());
                    VerbasIndenizatorias::create([
                        'deputado_id' => $deputado["id"],
                        'dataReferencia' => $date->toDateString(),
                        'quantidade' => $value,
                    ]);
                }


                list($usec, $sec) = explode(' ', microtime());
                $script_end = (float) $sec + (float) $usec;
                $elapsed_time = round($script_end - $script_start, 5);

//                dd($elapsed_time);
//                sleep(1 - $elapsed_time + 0.1);
                sleep(1);

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
