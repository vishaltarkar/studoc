<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateQuestion extends Command
{
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
        $choice = $this->choice(">>> Create Question: <<<", $this->initialChoices());

        switch ($choice) {
            case 'add':
                $this->createQuestionChoices();
                break;

            case 'back':
                $this->call('qanda:interactive');
                break;

            case 'exit':
                $this->info("Good Bye!");
                return 0;
                break;

            default:
                $this->error("Error!");
                break;
        }

        $this->call('question:create');
    }

    private function initialChoices()
    {
        return [
            "add" => "Add question",
            "back" => "Go back",
            "exit" => "exit"
        ];
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
            $answer = \App\Models\QuestionAnswer::create(["question_id" => $question->id, "answer" => $answer_text]);
        }
    }
}
