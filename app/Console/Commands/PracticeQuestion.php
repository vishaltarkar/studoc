<?php

namespace App\Console\Commands;

use App\Console\BaseQuestionCommand;
use Illuminate\Support\Facades\Log;

class PracticeQuestion extends BaseQuestionCommand
{
    public const SUB_MENU_TITLE_TXT = "Please select any one of the option below";

    public const NEXT_SLUG = "next";
    public const NEXT_TXT = "Select new question to answer";

    public const ANS_QUESTION_INSTRUCTION_TXT = "[ Please answer the below question. ]";

    public const SELECT_QUESTION_ID_TXT = 'Select the #Id of the question to answer it';
    public const ENTER_QUESTION_ANSWER_TXT = 'Please select option from above table as your answer';

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


    public static function getConstSubMenuTxt() :string
    {
        return self::SUB_MENU_TITLE_TXT;
    }

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
        $result = \App\Models\Question::getResults();

        if ($result['total'] == 0) {
            $this->newLine();
            $this->error("No Question Data. Please create some for practice!");
            $this->newLine();
            $this->backCommand();
        } else if ($result['total'] === $result['correct_count']) {
            $this->newLine();
            $this->error("All questions are answered.");
            $this->newLine();
            $this->backCommand();
        } else {
            $this->questionStatusTable($result);
            // select the question to answer
            $question = $this->selectQuestion();

            // get answer and store it in DB
            $this->answeringQuestion($question);
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
     * @return object
     */
    private function selectQuestion()
    {
        $this->newLine(2);
        $question_id = $this->promptInputWithValidation("Select the #Id of the question to answer it", 'question_id', 20);

        $question = \App\Models\Question::with(['options', 'result'])->find($question_id);
        if ($question) {
            if (@$question->result && @$question->result->is_correct === 1) {
                $this->info('Question is already answered.');
                $question = $this->selectQuestion();
            }
            return $question;
        } else {
            throw new \Exception;
            $this->error('Unknown Question choice.');
            return $this->selectQuestion();
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
                $this->error("Unknown option choice.");
                return $this->answeringQuestion($question); // retry on invalid entry
            }
            $this->newLine(1);

            // check answer
            if ($question_option->id == $question->answer_id) {
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
                'answer_id' => $question_option->id,
                'is_correct' => $is_correct
            ]);

            $this->newLine(1);
        } catch (\Exception $e) {
            $this->error("Something went wrong, Please try again. <<" . $e->getMessage());
            $this->handle();
        }
    }
}
