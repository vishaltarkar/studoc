<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\Question;
use App\Console\BaseQuestionCommand;
use App\Console\Commands\QuestionDash;
use App\Console\Commands\PracticeQuestion;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PracticeQuestionCommandTest extends TestCase
{
    use WithFaker, RefreshDatabase;

    protected $main_menu = [
        QuestionDash::CREATE_QUESTION_TXT, QuestionDash::DELETE_QUESTION_TXT,
        QuestionDash::LIST_QUESTION_TXT, QuestionDash::PRACTICE_QUESTION_TXT,
        QuestionDash::STATS_TXT, QuestionDash::RESET_TXT,
        BaseQuestionCommand::BACK_LBL, BaseQuestionCommand::QUIT_LBL,
        QuestionDash::CREATE_QUESTION_SLUG, QuestionDash::DELETE_QUESTION_SLUG,
        QuestionDash::LIST_QUESTION_SLUG, QuestionDash::PRACTICE_QUESTION_SLUG,
        QuestionDash::STATS_SLUG, QuestionDash::RESET_SLUG,
        BaseQuestionCommand::BACK_SLUG, BaseQuestionCommand::QUIT_SLUG,
    ];

    /**
     * A feature to test a practive question answer
     *
     * @test
     */
    public function practice_a_question_answer_successful()
    {
        #arrange
        $question_data = ['question' => $this->faker->sentence()];
        $question = Question::create($question_data); // creaet a question
        $answer = null;
        for ($i = 0; $i < 3; $i++) {
            $option = $question->options()->create(['option_txt' => $this->faker->word()]);
            if ($i === 0) {
                $answer = $option->option_txt;
                $question->update(["answer_id" => $option->id]);
            }
        }
        $end_choices = [PracticeQuestion::NEXT_TXT, BaseQuestionCommand::BACK_LBL, BaseQuestionCommand::QUIT_LBL, PracticeQuestion::NEXT_SLUG, BaseQuestionCommand::BACK_SLUG, BaseQuestionCommand::QUIT_SLUG];

        #act
        $cmd = $this->artisan('question:practice');
        $cmd->expectsQuestion(PracticeQuestion::SELECT_QUESTION_ID_TXT, $question->id);
        $cmd->expectsQuestion(PracticeQuestion::ENTER_QUESTION_ANSWER_TXT, $answer);

        $cmd->expectsChoice(PracticeQuestion::getConstSubMenuTxt(), BaseQuestionCommand::QUIT_SLUG, $end_choices);
        $cmd->expectsConfirmation(BaseQuestionCommand::QUIT_TXT, 'yes');

        #assert
        $cmd->assertExitCode(0);
    }

    /**
     * Feature test where all questions are answered correctly.
     *
     * @test
     */
    public function all_question_are_already_answered()
    {
        #arrange
        $question_data = ['question' => $this->faker->sentence()];
        $question = Question::create($question_data); // creaet a question

        // add 3 option for the question
        for ($i = 0; $i < 3; $i++) {
            $option = $question->options()->create(['option_txt' => $this->faker->word()]);
            if ($i === 0) {
                $answer = $option->option_txt;
                $question->update(["answer_id" => $option->id]);
            }
        }

        // Question Answered
        $question->result()->create([
            'question_id' => $question->id,
            'answer_id' => $question->answer_id,
            'is_correct' => true,
        ]);

        #act
        $cmd = $this->artisan('question:practice');
        $cmd->expectsChoice(QuestionDash::MENU_TITLE_TXT, 'quit', $this->main_menu);
        $cmd->expectsConfirmation(BaseQuestionCommand::QUIT_TXT, 'yes');

        #assert
        $cmd->assertExitCode(0);
    }

    /**
     * Feature test when there are no question in database to answer
     *
     * @test
     */
    public function no_question_to_answer()
    {
        $this->artisan('question:practice')
            ->expectsChoice(QuestionDash::MENU_TITLE_TXT, 'quit', $this->main_menu)
            ->expectsConfirmation(BaseQuestionCommand::QUIT_TXT, 'yes')
            ->assertExitCode(0);
    }
}
