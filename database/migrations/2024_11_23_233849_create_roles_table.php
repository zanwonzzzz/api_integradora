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
        Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('rol');
            $table->string('descripción')->nullable();
            $table->timestamps();
        });

        DB::table('roles')->insert([
            ['rol' => 'guest', 'descripción' => 'Invitado'],
            ['rol' => 'user', 'descripción' => 'Usuario'],
            ['rol' => 'admin', 'descripción' => 'Administrador'],
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('roles');
    }
};
