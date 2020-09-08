<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->point('location')->nullable();
            $table->string('username')->nullable();
            $table->string('password')->nullable();
            $table->string('timezone')->nullable();
            $table->string('device_token')->nullable();
            $table->boolean('email_confirm')->default(0);
            $table->string('email')->unique()->nullable();
            $table->string('phone')->unique()->nullable();
            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
