<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Console\BaseQuestionCommand;
use App\Console\Commands\CreateQuestion;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class CreateQuestionCommandTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    public $choice_list = [
        CreateQuestion::ADD_QUESTION_TXT, BaseQuestionCommand::BACK_LBL,
        BaseQuestionCommand::QUIT_LBL, CreateQuestion::ADD_QUESTION_SLUG,
        BaseQuestionCommand::BACK_SLUG, BaseQuestionCommand::QUIT_SLUG
    ];

    /**
     * A feature test to create a question with its 3 options
     *
     * @test
     */
    public function successfully_create_a_question_with_options()
    {
        #arrange
        $dummy_question = $this->faker->sentence();
        $dummp_options = $this->faker->word(). ",". $this->faker->word(). ",". $this->faker->word();

        #act
        $cmd = $this->artisan('question:create');
        $cmd->expectsChoice(CreateQuestion::SUB_MENU_TITLE_TXT, CreateQuestion::ADD_QUESTION_SLUG, $this->choice_list);
        $cmd->expectsQuestion(CreateQuestion::CREATE_QUESTION_TXT, $dummy_question);
        $cmd->expectsQuestion(CreateQuestion::ENTER_OPTION_TXT, $dummp_options);
        $cmd->expectsChoice(CreateQuestion::SUB_MENU_TITLE_TXT, BaseQuestionCommand::QUIT_SLUG, $this->choice_list);
        $cmd->expectsConfirmation(BaseQuestionCommand::QUIT_TXT, 'yes');

        #assert
        $cmd->assertExitCode(0);
    }

    /**
     * When Invalid Choice entered
     *
     * @test
     */
    public function invalid_question_create_entry_in_sub_menu()
    {
        #arrange
        $invalid_choice = 'invalid_choice';

        #act
        $cmd = $this->artisan('question:create');
        $cmd->expectsChoice(CreateQuestion::SUB_MENU_TITLE_TXT, $invalid_choice, $this->choice_list);

        #assert
        $cmd->assertExitCode(0);
    }

    /**
     * Try to create a question with only 2 option
     *
     * @test
     */
    public function create_question_with_only_2_option()
    {
        #arrange
        $dummy_question = $this->faker->sentence();
        $dummp_option = $this->faker->word(). ",". $this->faker->word();

        #act
        $cmd = $this->artisan('question:create');
        $cmd->expectsChoice(CreateQuestion::SUB_MENU_TITLE_TXT, CreateQuestion::ADD_QUESTION_SLUG, $this->choice_list);
        $cmd->expectsQuestion(CreateQuestion::CREATE_QUESTION_TXT, $dummy_question);
        $cmd->expectsQuestion(CreateQuestion::ENTER_OPTION_TXT, $dummp_option);
        // $cmd->expectsOutput('Invalid Option Entry, Try Again!');
        $cmd->expectsConfirmation(CreateQuestion::START_OVER_TXT, 'yes');
        $cmd->expectsChoice(CreateQuestion::SUB_MENU_TITLE_TXT, BaseQuestionCommand::QUIT_SLUG, $this->choice_list);
        $cmd->expectsConfirmation(BaseQuestionCommand::QUIT_TXT, 'yes');

        #assert
        $cmd->assertExitCode(0);
    }
}
