<?php

use App\Like;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateNewsFeedsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('news_feeds', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unsigned()->index();
            $table->foreign('user_id')
                ->references('id')
                ->on('users')
                ->onDelete('cascade');
            $table->string('lat')->nullable();
            $table->string('lng')->nullable();
            $table->longText('content')->nullable();
            $table->longText('photos')->nullable();
            $table->string('type')->default('text')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
        });

        Schema::create('post_tags', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('post_id')->unsigned()->index();
            $table->integer('user_id')->unsigned()->index();
            $table->string('mode')->nullable();
            $table->timestamps();
        });
    }

    /*
     * Each post belongs to many likes
     */
    public function likes()
    {
        return $this->belongsToMany(Like::class, 'like_post', 'like_id', 'post_id');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('news_feeds');
    }
}
