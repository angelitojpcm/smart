<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class CreateStateForPaymentSaleNote extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
       //crea una columna enum para sale_notes, los valores son en espera, aprobado y rechazado, pero en ingles
       Schema::table('sale_notes',function (Blueprint $table){
        $table->enum('state_payment_id',['01','02','03'])->default('01')->comment('01: En espera, 02: Aprobado, 03: Rechazado');

    });

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
      
        Schema::table('sale_notes',function (Blueprint $table){
            $table->dropColumn('state_payment_id');
        });
    }
}
