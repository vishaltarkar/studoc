<?php

namespace App\Console\Commands;

use App\Models\Question;
use App\Models\QuestionResult;
use Illuminate\Support\Facades\Log;
use App\Console\BaseQuestionCommand;

class PracticeQuestion extends BaseQuestionCommand
{
    const SUB_MENU_TITLE_TXT = "Please select any one of the option below";

    const NEXT_SLUG = "next";
    const NEXT_TXT = "Select new question to answer";

    const ANS_QUESTION_INSTRUCTION_TXT = "[ Please answer the below question. ]";

    const SELECT_QUESTION_ID_TXT = 'Select the #Id of the question to answer it';
    const ENTER_QUESTION_ANSWER_TXT = 'Please select option from above table as your answer';

    const NO_QUESTION_ERR_TXT = "No Question Data. Please create some for practice!";
    const ALL_QUESTION_ANSWERED_ERR_TXT = "All questions are answered.";

    const QUESTION_IS_ALREADY_ANSWER_TXT = "Question is already answered.";

    const UNKNOWN_QUESTION_CHOICE_TXT = "Unknown Question choice.";
    const UNKNOWN_OPTION_CHOICE_TXT = "Unknown option choice.";

    const EXIT_TO_PREV_MENU_CONFIRM_TXT = "Enter `yes` to try again? and `no` to go previous menu.";

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
    protected const PRACTICE_WC_CMNT = "Question list with result";


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

        // get question & result data;
        $result = Question::getResults();

        if ($result['total'] == 0) {
            $this->error(self::NO_QUESTION_ERR_TXT);
            $this->backCommand();
        } else if ($result['total'] === $result['correct_count']) {
            $this->error(self::ALL_QUESTION_ANSWERED_ERR_TXT);
            $this->backCommand();
        } else {
            $this->questionStatusTable($result);
            // select the question to answer
            if ($question = $this->selectQuestion()) {
                // get answer and store it in DB
                $this->answeringQuestion($question);
            }
            parent::handle();
        }
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
    private function questionStatusTable($result)
    {
        // get result
        $bar = $this->output->createProgressBar($result['total']);
        $bar->advance($result['correct_count']);
        $this->newLine();
        // generate table with status
        $this->table(
            ['#', 'Question', 'Status'],
            $result['list']
        );
        $this->info('Completion in percentage ' . $result['correct_perc'] . '%'); // stats
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
     * @return object|null
     */
    private function selectQuestion()
    {
        $question_id = $this->promptInputWithValidation(self::SELECT_QUESTION_ID_TXT, 'question_id', 20);

        $question = Question::with(['options', 'result'])->find($question_id);
        if ($question) {
            if (@$question->result && @$question->result->is_correct === 1) {
                $this->info(self::QUESTION_IS_ALREADY_ANSWER_TXT);
                $question = $this->selectQuestion();
            }
            return $question;
        } else {
            $this->error(self::UNKNOWN_QUESTION_CHOICE_TXT);
            if ($this->confirm(self::EXIT_TO_PREV_MENU_CONFIRM_TXT)) {
                return $this->selectQuestion();
            } else {
                return null;
            }
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
            $this->info(self::ANS_QUESTION_INSTRUCTION_TXT);
            $this->info($question->question);

            // display ans options
            $option_list = $question->options()->select('option_txt')->get();

            $this->table(
                ['Option'],
                $option_list,
            );
            $this->newLine(1);
            $user_answer = $this->promptInputWithValidation(self::ENTER_QUESTION_ANSWER_TXT, 'Answer', 20);

            // validate answer text
            $question_option = $question->options()->where('option_txt', 'like', $user_answer)->first();
            if (!$question_option) {
                $this->error(self::UNKNOWN_OPTION_CHOICE_TXT);
                if ($this->confirm(self::EXIT_TO_PREV_MENU_CONFIRM_TXT)) {
                    return $this->answeringQuestion($question); // retry on invalid entry
                } else {
                    return null;
                }
            }
            // check answer
            if ($question_option->id == $question->answer_id) {
                $this->comment('<Correct>');
                $is_correct = 1;
            } else {
                $this->error('<Incorrect>');
                $is_correct = 0;
            }

            // store user answer
            QuestionResult::updateOrCreate([
                'question_id' => $question->id,
            ], [
                'answer_id' => $question_option->id,
                'is_correct' => $is_correct
            ]);
        } catch (\Exception $e) {
            $this->error("Something went wrong, Please try again. <<" . $e->getMessage());
            $this->handle();
        }
    }
}
