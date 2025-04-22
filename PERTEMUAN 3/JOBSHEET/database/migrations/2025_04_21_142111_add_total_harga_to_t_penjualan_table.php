<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTotalHargaToTPenjualanTable extends Migration
{
    public function up()
    {
        Schema::table('t_penjualan', function (Blueprint $table) {
            $table->decimal('total_harga', 15, 2)->default(0)->after('penjualan_tanggal');
        });
    }

    public function down()
    {
        Schema::table('t_penjualan', function (Blueprint $table) {
            $table->dropColumn('total_harga');
        });
    }
}