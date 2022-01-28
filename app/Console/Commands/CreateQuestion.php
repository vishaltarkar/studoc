<?php

namespace App\Console\Commands;

use App\Console\BaseQuestionCommand;

class CreateQuestion extends BaseQuestionCommand
{

    protected const ADD_QUESTION_SLUG = 'add';
    protected const ADD_QUESTION_TXT = "Add question";

    protected const SUB_MENU_TITLE_TXT = "Create Question";

    protected const SUB_MENU = [
        self::ADD_QUESTION_SLUG => self::ADD_QUESTION_TXT
    ];

    protected const CREATE_QUESTION_TXT = "Please enter the question.";
    protected const ENTER_OPTION_TXT = "Please enter the question's option #";
    protected const SELECT_CORRECT_OPTION_TXT = "Please select the correct option";
    protected const QUESTION_ADD_SUCCESS_TXT = "Question successfully added.";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'question:create';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a Question';

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
        parent::handle();
        $this->call('question:create');
    }

    /**
     * @return string
     */
    protected function menuTitle(): string
    {
        return self::SUB_MENU_TITLE_TXT;
    }

    protected function backCommand()
    {
        $this->call('qanda:interactive');
    }

    protected function menuChoices()
    {
        return self::SUB_MENU;
    }

    protected function handleMenuChoices(string $choice)
    {
        switch ($choice) {
            case self::ADD_QUESTION_SLUG:
                $this->createQuestionChoices();
                break;
        }
    }

    private function createQuestionChoices()
    {
        // ask user for the questions
        $question_text = $this->promptInputWithValidation(self::CREATE_QUESTION_TXT, 'Question');
        // $answer_text = $this->promptInputWithValidation(self::ENTER_OPTION_TXT, 'Answer');

        // save question to db
        // $data = ['question' => $question_text, 'answer' => $answer_text];
        $data = ['question' => $question_text];
        $question = \App\Models\Question::create($data);

        $option_list = $this->addQuestionAnswers($question); // pass the question for add its options
        $this->selectCorrectOption($question, $option_list);
    }

    /**
     * Prompt for possible options for a question
     *
     * @param object $question
     * @return array {'id': int, "option": string}
     */
    private function addQuestionAnswers($question)
    {
        $option_list = [];
        for ($i=0; $i < 4; $i++) {
            $answer_text = $this->promptInputWithValidation(self::ENTER_OPTION_TXT . " - ". $i+1, 'Option');
            $option = $question->options()->create(['option_txt' => $answer_text]); // create option for the question
            $option_list[] = ["id" => $option->id, "option" => $answer_text];
        }
        return $option_list;
    }

    /**
     * Select Correct option for the question
     *
     * @param object $question
     * @param array {'id': int, "option": string}
     * @return void
     */
    private function selectCorrectOption($question, $option_list)
    {
        $this->table(
            ['#', 'Options'],
            $option_list,
        );
        $this->newLine(1);

        $answer_text = $this->promptInputWithValidation(self::SELECT_CORRECT_OPTION_TXT, 'Option');
        // validate answer text
        $question_option = \App\Models\QuestionOption::find($answer_text);

        if (!$question_option) {
            $this->error("Unknown option choice.");
            return $this->selectCorrectOption($question, $option_list); // retry on incorrecet entry
        }

        $question->update(['answer_id' => $answer_text]); // update answer_id to the question

        $this->info(self::QUESTION_ADD_SUCCESS_TXT);
    }
}
