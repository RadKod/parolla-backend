<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateCustomQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('custom_questions', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('lang');
            $table->string('fingerprint')->nullable();
            $table->boolean('is_public')->default(false);
            $table->boolean('is_anon')->default(true);
            $table->json('qa_list');
            $table->string('room');
            $table->integer('view_count')->default(0);
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
        Schema::dropIfExists('custom_questions');
    }
}
