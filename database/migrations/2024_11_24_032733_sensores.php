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
            $table->string('Identificador');
            $table->softDeletes();
            $table->timestamps();
        });

        DB::table('sensores')->insert([
            ['Nombre_Sensor' => 'Temperatura','Identificador' => 'TEM'],
            ['Nombre_Sensor' => 'Movimiento','Identificador' => 'PIR'],
            ['Nombre_Sensor' => 'Sonido','Identificador' => 'SON'],
            ['Nombre_Sensor' => 'Gas','Identificador' => 'GAS'],
            ['Nombre_Sensor' => 'Luz','Identificador' => 'LUZ'],
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
