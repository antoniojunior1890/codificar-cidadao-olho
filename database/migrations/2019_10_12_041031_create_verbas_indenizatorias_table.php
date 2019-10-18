<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateVerbasIndenizatoriasTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verbas_indenizatorias', function (Blueprint $table) {
//            $table->integer('codTipoDespesa')->primary();
            $table->bigIncrements('id');
            $table->integer('deputado_id');
//            $table->foreign('deputado_id')
//                ->references('id')
//                ->on('deputados');
            $table->date('dataReferencia');
            $table->integer('quantidade');
//            $table->string('valor');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('verbas_indenizatorias');
    }
}
