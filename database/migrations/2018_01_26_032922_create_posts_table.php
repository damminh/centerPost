<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePostsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('posts', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('member_id');
            $table->tinyInteger('user_id');
            $table->integer('requirement_id');
            $table->integer('domain_id');
            $table->string('title');
            $table->string('content');
            $table->integer('category_id');
            //link to post in website
            $table->string('link')->nullable();
            //id in website
            $table->integer('post_website_id')->nullable();
            $table->text('description')->nullable();
            $table->tinyInteger('is_main');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('posts');
    }
}
