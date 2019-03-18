<?php

use App\NewsFeed;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateLikesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('likes', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('like_id')->unsigned();
            $table->integer('post_id')->unsigned();
            $table->softDeletes();
            $table->timestamps();
        });
    }

//    /*
//     * Each post belongs to many likes
//     */
//    public function likes()
//    {
//        return $this->belongsToMany(NewsFeed::class, 'like_post', 'like_id', 'post_id');
//    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('likes');
    }
}
