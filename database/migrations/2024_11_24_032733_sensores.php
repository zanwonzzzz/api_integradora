<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sensores', function (Blueprint $table) {
            $table->id();
            $table->string('Nombre_Sensor');
            $table->softDeletes();
            $table->timestamps();
        });

        DB::table('sensores')->insert([
            ['Nombre_Sensor' => 'gas'],
            ['Nombre_Sensor' => 'temperatura'],
            ['Nombre_Sensor' => 'movimiento'],
            ['Nombre_Sensor' => 'sonido'],
            ['Nombre_Sensor' => 'luz'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sensores');
    }
};
