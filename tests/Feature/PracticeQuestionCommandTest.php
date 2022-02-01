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

    public function test_unknown_question_choice_with_quit()
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
        $cmd->expectsQuestion(PracticeQuestion::SELECT_QUESTION_ID_TXT, 999999999);
        $cmd->expectsOutput(PracticeQuestion::UNKNOWN_QUESTION_CHOICE_TXT);
        $cmd->expectsConfirmation(PracticeQuestion::EXIT_TO_PREV_MENU_CONFIRM_TXT, 'no');
        $cmd->expectsChoice(PracticeQuestion::SUB_MENU_TITLE_TXT, BaseQuestionCommand::QUIT_SLUG, $end_choices);
        $cmd->expectsConfirmation(BaseQuestionCommand::QUIT_TXT, 'yes');

        #assert
        $cmd->assertExitCode(0);
    }

    /**
     * A feature to test a practice question with correct answer with quit
     *
     * @test
     */
    public function practice_question_with_correct_answer_with_quit()
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
        $cmd->expectsOutput('<Correct>');
        $cmd->expectsChoice(PracticeQuestion::SUB_MENU_TITLE_TXT, BaseQuestionCommand::QUIT_SLUG, $end_choices);
        $cmd->expectsConfirmation(BaseQuestionCommand::QUIT_TXT, 'yes');

        #assert
        $cmd->assertExitCode(0);
    }

    /**
     * A feature to test a practice question with incorrect answer with quit
     *
     * @test
     */
    public function practice_question_with_incorrect_answer_with_quit()
    {
        #arrange
        $question_data = ['question' => $this->faker->sentence()];
        $question = Question::create($question_data); // creaet a question
        $answer = null;
        for ($i = 0; $i < 3; $i++) {
            $option = $question->options()->create(['option_txt' => $this->faker->word()]);
            if ($i === 0) {
                $question->update(["answer_id" => $option->id]);
            }
            $answer = $option->option_txt;
        }
        $end_choices = [PracticeQuestion::NEXT_TXT, BaseQuestionCommand::BACK_LBL, BaseQuestionCommand::QUIT_LBL, PracticeQuestion::NEXT_SLUG, BaseQuestionCommand::BACK_SLUG, BaseQuestionCommand::QUIT_SLUG];

        #act
        $cmd = $this->artisan('question:practice');
        $cmd->expectsQuestion(PracticeQuestion::SELECT_QUESTION_ID_TXT, $question->id);
        $cmd->expectsQuestion(PracticeQuestion::ENTER_QUESTION_ANSWER_TXT, $answer);
        $cmd->expectsOutput('<Incorrect>');
        $cmd->expectsChoice(PracticeQuestion::SUB_MENU_TITLE_TXT, BaseQuestionCommand::QUIT_SLUG, $end_choices);
        $cmd->expectsConfirmation(BaseQuestionCommand::QUIT_TXT, 'yes');

        #assert
        $cmd->assertExitCode(0);
    }

    /** @test */
    public function practice_question_with_invalid_answer_with_quit()
    {
        #arrange
        $question_data = ['question' => $this->faker->sentence()];
        $question = Question::create($question_data); // creaet a question
        $answer = null;
        for ($i = 0; $i < 3; $i++) {
            $option = $question->options()->create(['option_txt' => $this->faker->word()]);
            if ($i === 0) {
                $question->update(["answer_id" => $option->id]);
            }
            $answer = $option->option_txt;
        }
        $end_choices = [PracticeQuestion::NEXT_TXT, BaseQuestionCommand::BACK_LBL, BaseQuestionCommand::QUIT_LBL, PracticeQuestion::NEXT_SLUG, BaseQuestionCommand::BACK_SLUG, BaseQuestionCommand::QUIT_SLUG];

        #act
        $cmd = $this->artisan('question:practice');
        $cmd->expectsQuestion(PracticeQuestion::SELECT_QUESTION_ID_TXT, $question->id);
        $cmd->expectsQuestion(PracticeQuestion::ENTER_QUESTION_ANSWER_TXT, "unknown_choice");
        $cmd->expectsOutput(PracticeQuestion::UNKNOWN_OPTION_CHOICE_TXT);
        $cmd->expectsConfirmation(PracticeQuestion::EXIT_TO_PREV_MENU_CONFIRM_TXT, 'no');
        $cmd->expectsChoice(PracticeQuestion::SUB_MENU_TITLE_TXT, BaseQuestionCommand::QUIT_SLUG, $end_choices);
        $cmd->expectsConfirmation(BaseQuestionCommand::QUIT_TXT, 'yes');

        #assert
        $cmd->assertExitCode(0);
    }

    /**
     * Feature test where all questions are answered already.
     *
     * @test
     */
    public function all_question_are_already_answered_with_quit()
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
        $cmd->expectsOutput(PracticeQuestion::ALL_QUESTION_ANSWERED_ERR_TXT);
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
    public function no_question_to_answer_with_quit()
    {
        $this->artisan('question:practice')
            ->expectsOutput(PracticeQuestion::NO_QUESTION_ERR_TXT)
            ->expectsChoice(QuestionDash::MENU_TITLE_TXT, 'quit', $this->main_menu)
            ->expectsConfirmation(BaseQuestionCommand::QUIT_TXT, 'yes')
            ->assertExitCode(0);
    }
}
