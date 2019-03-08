<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFoodCategoriesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('food_categories', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->nullable();
            $table->longText('photo')->nullable();
            $table->timestamps();
        });

        Schema::create('food_profile', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('food_id')->unsigned()->index()->nullable();
            $table->integer('profile_id')->unsigned()->index()->nullable();
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
        Schema::dropIfExists('food_categories');
        Schema::dropIfExists('food_profile');
    }
}
