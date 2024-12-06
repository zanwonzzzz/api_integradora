<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('infosensores', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('sensor_id');
            $table->text('valor');
            $table->softDeletes();
            $table->timestamps();

            $table->foreign('sensor_id')->references('id')->on('sensores')->onDelete('cascade');


        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('infosensores');
    }
};
