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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->string('password');
            $table->unsignedBigInteger('rol_id')->default(2);
            $table->boolean('cuenta_activa')->default(false);
            $table->text('fotoperfil')->nullable();
            $table->string('mime_type')->nullable();
            $table->boolean('monitor')->default(false)->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->rememberToken();
            $table->softDeletes();
            $table->timestamps();


            $table->foreign('rol_id')->references('id')->on('roles')->onDelete('cascade');
        });

        DB::table('users')->insert([
            'name' => 'admin',
            'email' => 'babysaveus@gmail.com',
            'password' => bcrypt('12345678'),
            'rol_id' => 3,
            'cuenta_activa' => true,
        ]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
};
