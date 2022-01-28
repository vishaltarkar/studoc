<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AdjustQuestionAndQuestionResultTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('questions', function (Blueprint $table) {
            $table->dropColumn('answer');
            $table->foreignId('answer_id')->after('question')->nullable();
            $table->index(['answer_id']);
            $table->foreign('answer_id')
                ->references('id')
                ->on('question_options')
                ->onDelete('cascade');
        });

        Schema::table('question_results', function (Blueprint $table) {
            $table->dropColumn('answer_value');
            $table->foreignId('answer_id')->after('question_id');
            $table->index(['answer_id']);
            $table->foreign('answer_id')
                ->references('id')
                ->on('question_options')
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
        Schema::table('question_results', function (Blueprint $table) {
            $table->dropForeign('answer_id');
            $table->dropColumn('answer_id');
            $table->string('answer_value')->after('question_id');
        });

        Schema::table('questions', function (Blueprint $table) {
            $table->string('answer')->after('question');
        });
    }
}
