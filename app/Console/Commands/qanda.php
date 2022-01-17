<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class qanda extends Command
{
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
        $this->info('Welcome to the Quiz !!');
        $choice = $this->choice(">>> Please select any one of the option below: <<< ", $this->initialChoices());
        switch ($choice) {
            case 'create':
                $this->call('question:create');
                break;

            case 'list':
                $this->call('question:list');
                break;

            case 'practice':
                $this->call('question:practice');
                break;

            case 'stats':
                $this->stats();
                break;

            case 'reset':
                $this->resetApp();
                break;

            case 'exit':
                $this->exitSession();
                break;

            default:
                $this->error('Error Msg : Unknown choice');
                $this->call('qanda:interactive');
                break;
        }
    }

    // Exit from the session
    private function exitSession()
    {
        if ($this->confirm('Are you sure you want to exit')) {
            $this->info("Thank you, Have a nice day!");
        } else {
            $this->call('qanda:interactive');
        }
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
        $this->call('qanda:interactive');
    }

    private function stats()
    {
        $stats = $this->getStatData();
        $this->newLine(1);
        $this->info("The total questions : " . $stats['total']);
        $this->info("Pecentage of questions that have an answer. : " . round($stats['attempt_perc'], 2) . "%");
        $this->info("Pecentage of questions that have a correct answer. : " . round($stats['correct_perc'], 2). "%");
        $this->newLine(1);

        $this->call('qanda:interactive');
    }

    private function getStatData()
    {
        $correctCount = $attemptCount = 0;
        $questions = \App\Models\Question::with(['result'])->get();
        $questionCount = sizeof($questions);
        if ($questionCount > 0) {
            foreach ($questions as $key => $question) {

                if (@$question->result) {
                    $attemptCount++;
                    if ($question->result->is_correct === 1) {
                        $correctCount++;
                    }
                }
            }
        }

        // corret answer  %
        $correctPercent = '0';
        if ($correctCount > 0) {
            $correctPercent = ($correctCount/$questionCount) * 100;
        }

        // attempt question %
        $attemptPercentage = '0';
        if ($attemptCount > 0) {
            $attemptPercentage = ($attemptCount/$questionCount) * 100;
        }

        return [
            'total' => $questionCount,
            'correct_perc' => $correctPercent,
            'attempt_perc' => $attemptPercentage
        ];
    }


    private function initialChoices()
    {
        return [
            "create" => "Create a question",
            "list" => "List all questions",
            "practice" => "Practice",
            "stats" => "Stats",
            "reset" => "Reset",
            "exit" => "Exit"
        ];
    }
}
