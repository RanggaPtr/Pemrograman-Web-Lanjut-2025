<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTStokTotalTable extends Migration
{
    public function up()
    {
        Schema::create('t_stok_total', function (Blueprint $table) {
            $table->id('stok_total_id');
            $table->foreignId('barang_id')->constrained('m_barang', 'barang_id')->onDelete('cascade');
            $table->integer('stok_jumlah')->default(0);
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('t_stok_total');
    }
}