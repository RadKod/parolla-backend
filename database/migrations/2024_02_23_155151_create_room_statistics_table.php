<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRoomStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('room_statistics', function (Blueprint $table) {
            $table->id();
            $table->text('fingerprint');
            $table->bigInteger('room_id')->unsigned()->index();
            $table->foreign('room_id')->references('id')->on('custom_questions')->onDelete('cascade');
            $table->json('game_result');
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
        Schema::dropIfExists('room_statistics');
    }
}
