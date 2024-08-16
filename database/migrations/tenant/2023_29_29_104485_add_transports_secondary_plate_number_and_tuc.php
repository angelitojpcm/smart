<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddTransportsSecondaryPlateNumberAndTuc extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
      

            Schema::table('transports', function(Blueprint $table){
                $table->string('secondary_plate_number')->nullable()->after('plate_number');
                $table->string('tuc')->nullable()->after('secondary_plate_number');
            });
         
     
    }



    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
                Schema::table('transports', function(Blueprint $table){
                    $table->dropColumn('tuc');
                    $table->dropColumn('secondary_plate_number');
                });
            
    

      
    }
}
