<?php

namespace App\Console\Commands;

use App\Console\BaseQuestionCommand;

class PracticeQuestion extends BaseQuestionCommand
{
    protected const SUB_MENU_TITLE_TXT = "Please select any one of the option below";

    protected const NEXT_SLUG = "next";
    protected const NEXT_TXT = "Select new question to answer";

    protected const SUB_MENU = [
        self::NEXT_SLUG => self::NEXT_TXT
    ];

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'question:practice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start Practicing the questions';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected const PRACTICE_WC_MSG = "Welcome to Practice";
    protected const PRACTICE_WC_CMNT = "Here is the list of question with result";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        // welcome message
        $this->info(self::PRACTICE_WC_MSG);

        $this->comment(self::PRACTICE_WC_CMNT);

        $this->questionStatusTable();

        parent::handle();

    }

    /**
     * Back command
     *
     */
    protected function backCommand()
    {
        $this->call('qanda:interactive');
    }

    /**
     * Generate Table to display Question & their Status
     *
     * @return void
     */
    private function questionStatusTable()
    {
        // get result
        $result = \App\Models\Question::getResults();


        $bar = $this->output->createProgressBar($result['total']);
        $bar->advance($result['correct_count']);
        $this->newLine();

        // generate table with status
        $this->table(
            ['#', 'Question', 'Status'],
            $result['list']
        );

        $this->newLine();
        $this->info('Completion in percentage ' . $result['correct_perc'] . '%'); // stats
        $this->newLine();

        // select the question to answer
        $question = $this->selectQuestion();

        // get answer and store it in DB
        $this->answeringQuestion($question);
    }

    /**
     * Title of Menu
     * @return string
     */
    protected function menuTitle(): string
    {
        return self::SUB_MENU_TITLE_TXT;
    }

    /**
     * Array of Menu Choices
     *
     * @return array
     */
    protected function menuChoices()
    {
        return self::SUB_MENU;
    }

    /**
     * Handle Menu for practice question
     *
     * @param string $choice
     * @return void
     */
    protected function handleMenuChoices(string $choice)
    {
        switch ($choice) {
            case self::NEXT_SLUG:
                $this->call('question:practice');
                break;
        }
    }

    /**
     * Ask user to select id of the question to answer
     *
     * @return object|void
     */
    private function selectQuestion()
    {
        $this->newLine(2);
        $question_id = $this->promptInputWithValidation("Select the #Id of the question to answer it", 'question_id', 20);
        $question = \App\Models\Question::with(['result'])->find($question_id);
        if ($question) {
            if (@$question->result && @$question->result->is_correct === 1) {
                $this->info('Question is already answered.');
                $this->selectQuestion();
            }
            return $question;
        } else {
            $this->error('Unknown Question choice.');
            $this->selectQuestion();
        }
    }

    /**
     * Check if answer is correct or not and store it in DB
     *
     * @param object $question
     * @return void
     */
    public function answeringQuestion($question) {
        try {
            $this->newLine(1);
            $this->info("Please answer the below question.");

            $user_answer = $this->promptInputWithValidation($question->question, 'Answer', 255);

            // check answer
            if (\Illuminate\Support\Str::lower($user_answer) == \Illuminate\Support\Str::lower($question->answer)) {
                $this->comment('<Correct>');
                $is_correct = 1;
            } else {
                $this->error('<Incorrect>');
                $is_correct = 0;
            }

            // store user answer
            \App\Models\QuestionResult::updateOrCreate([
                'question_id' => $question->id,
            ], [
                'answer_value' => $user_answer,
                'is_correct' => $is_correct
            ]);

            $this->newLine(1);
        } catch (\Exception $e) {
            $this->error("Something went wrong, Please try again.");
            $this->questionStatusTable();
        }
    }
}
