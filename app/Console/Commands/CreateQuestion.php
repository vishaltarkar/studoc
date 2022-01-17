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
    protected const CREATE_ANSWER_TXT = "Please enter the question's answer.";

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
        $answer_text = $this->promptInputWithValidation(self::CREATE_ANSWER_TXT, 'Answer');

        // save question to db
        $data = ['question' => $question_text, 'answer' => $answer_text];
        \App\Models\Question::create($data);
    }
}
