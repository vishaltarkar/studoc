<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateQuestionResultsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('question_results', function (Blueprint $table) {
            $table->id();
            $table->foreignId('question_id');
            $table->string('answer_value')->nullable();
            $table->boolean('is_correct')->default(0)->nullable();
            $table->timestamps();

            $table->index(['question_id']);
            $table->foreign('question_id')
                ->references('id')
                ->on('questions')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('question_results');
    }
}
