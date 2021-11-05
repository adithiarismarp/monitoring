<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePerbaikanTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('perbaikan', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('daftar_perbaikan')->unsigned();
            $table->foreign('daftar_perbaikan')->references('id_produk')->on('produk');
            $table->integer('nama_customer')->unsigned();
            $table->foreign('nama_customer')->references('id_member')->on('member');
            $table->string('jenis', 30);
            $table->string('serial_number', 30);
            $table->string('model', 30);
            $table->string('dp', 20);
            $table->string('total', 20)->nullable();
            $table->string('status', 20);
            $table->string('kode_unik', 20)->unique();
            $table->string('note', 255);
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
        Schema::dropIfExists('perbaikan');
    }
}
