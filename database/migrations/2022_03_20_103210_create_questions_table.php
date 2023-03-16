<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('questions', static function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('alphabet_id');
            $table->string('question');
            $table->string('answer');
            $table->timestamp('release_at')->nullable();
            $table->timestamps();

            // foreign keys
            $table->foreign('alphabet_id')->references('id')
                ->on('alphabet')->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('questions');
    }
}
