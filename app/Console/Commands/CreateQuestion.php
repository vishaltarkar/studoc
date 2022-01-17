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
        $question_text = $this->ask("Please enter the question.");

        // save question to db
        $question = \App\Models\Question::create(['question' => $question_text]);

        if ($question) {
            // ask for question's answer
            $answer_text = $this->ask("Please enter answer to the question.");
            \App\Models\QuestionAnswer::create(["question_id" => $question->id, "answer" => $answer_text]);
        }
    }
}
