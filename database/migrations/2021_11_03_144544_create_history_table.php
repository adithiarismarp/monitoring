<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateHistoryTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('history', function (Blueprint $table) {
            $table->bigIncrements('id_history');
            $table->bigInteger('id_perbaikan')->unsigned();
            $table->foreign('id_perbaikan')->references('id')->on('perbaikan');
            $table->bigInteger('id_teknisi')->unsigned()->nullable();
            $table->foreign('id_teknisi')->references('id_supplier')->on('supplier');
            $table->string('progress', 30);
            $table->string('tgl');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history');
    }
}
