<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ListQuestion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'question:list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get list of questions';

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
        $questions = \App\Models\Question::with(['answer'])->get(); // get all questions
        // customize the object for the table
        $question_list = collect($questions)->map(function ($question){
            return ['id' => $question->id, 'question' => $question->question, 'answer' => $question->answer->option_txt];
        });
        // display question list
        $this->table(
            ['#', 'Question', 'Answer'],
            $question_list,
        );
        $this->newLine(1);
        $this->backCommand();
    }

    /**
     * a back command
     */
    protected function backCommand()
    {
        $this->call('qanda:interactive');
    }
}
