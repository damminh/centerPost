<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('requirements', function (Blueprint $table) {
            $table->increments('id');
            $table->tinyInteger('member_id');
            $table->tinyInteger('user_id');
            $table->integer('domain_id');
            $table->string('name');
            $table->dateTime('start_date');
            $table->dateTime('end_date');
            $table->integer('time_repeat')->nullable();
            //type is repeat or not
            $table->tinyInteger('type');
            $table->tinyInteger('priority');
            $table->text('description')->nullable();
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
        Schema::dropIfExists('requirements');
    }
}
