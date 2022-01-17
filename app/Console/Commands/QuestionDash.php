<?php
/**
 * Main Menu of Question Console App
 * @author akuma <vishal@tarkar.com>
 */

namespace App\Console\Commands;

use App\Console\BaseQuestionCommand;

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
                $this->stats();
                break;

            case self::RESET_SLUG:
                $this->resetApp();
                break;
        }
    }

    // Get Question Stats
    private function stats()
    {
        $stats = $this->getStatData();
        $this->newLine(1);
        $this->info('############################################################');
        $this->info("The total questions : " . $stats['total']);
        $this->info("Pecentage of questions that have an answer. : " . round($stats['attempt_perc'], 2) . "%");
        $this->info("Pecentage of questions that have a correct answer. : " . round($stats['correct_perc'], 2). "%");
        $this->info('############################################################');
        $this->newLine(1);

        $this->backCommand();
    }
    // Query Function for stats
    private function getStatData()
    {
        return \App\Models\Question::getStatsData();
    }

    // reset App data
    private function resetApp()
    {
        if ($this->confirm('Are you sure want to delete all questions and answers?')) {
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

            \App\Models\Question::truncate(); // truncate questions
            \App\Models\QuestionAnswer::truncate(); // truncate question's answer
            \App\Models\QuestionResult::truncate(); // truncate results

            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            $this->info("Questions and Answers has been reset.");
        }
        $this->backCommand();
    }
}
