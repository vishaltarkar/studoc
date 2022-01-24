<?php
/**
 * Main Menu of Question Console App
 * @author akuma <vishal@tarkar.com>
 */

namespace App\Console\Commands;

use \App\Console\BaseQuestionCommand;

class QuestionDash extends BaseQuestionCommand
{

    const WELCOME_TXT = "Welcome to the Question Console App!"; // welcome text of main app

    const MENU_TITLE_TXT = "Please select any of the option."; // menu title

    // menu choice
    const CREATE_QUESTION_TXT = 'Create a question';
    const CREATE_QUESTION_SLUG = 'create';

    const LIST_QUESTION_TXT = 'List Questions';
    const LIST_QUESTION_SLUG = 'list';

    const PRACTICE_QUESTION_TXT = 'Practice Questions';
    const PRACTICE_QUESTION_SLUG = 'practice';

    const STATS_TXT = 'Questions stats';
    const STATS_SLUG = 'stats';

    const RESET_TXT = "Reset Application data";
    const RESET_SLUG = "reset";

    // menu array
    const MAIN_MENU = [
        self::CREATE_QUESTION_SLUG => self::CREATE_QUESTION_TXT,
        self::LIST_QUESTION_SLUG => self::LIST_QUESTION_TXT,
        self::PRACTICE_QUESTION_SLUG => self::PRACTICE_QUESTION_TXT,
        self::STATS_SLUG => self::STATS_TXT,
        self::RESET_SLUG => self::RESET_TXT
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'qanda:interactive';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run command line questions and answers.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info(SELF::WELCOME_TXT); // display welcome txt
        parent::handle();
    }

    /**
     * Menu Choices
     *
     * @return array
     */
    protected function menuChoices() :array
    {
        return self::MAIN_MENU;
    }

    /**
     * Menu title text
     * @return string
     */
    protected function menuTitle(): string
    {
        return self::MENU_TITLE_TXT;
    }

    /**
     * a back command
     */
    protected function backCommand()
    {
        $this->call('qanda:interactive');
    }

    /**
     * function handle the menu choices of user
     *
     * @param string $choice
     * @return void
     */
    protected function handleMenuChoices(string $choice)
    {
        switch ($choice) {
            case self::CREATE_QUESTION_SLUG:
                $this->call('question:create');
                break;

            case self::LIST_QUESTION_SLUG:
                $this->call('question:list');
                break;

            case self::PRACTICE_QUESTION_SLUG:
                $this->call('question:practice');
                break;

            case self::STATS_SLUG:
                $this->call('question:stats');
                break;

            case self::RESET_SLUG:
                $this->call('question:reset-result');
                break;
        }
    }
}
