<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class DeleteQuestion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'question:delete';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Delete a Question';

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
        // list of questions
        $this->table(
            ['#', 'Question'],
            \App\Models\Question::all(['id', 'question'])->toArray(),
        );

        $question_id = $this->ask("Please enter an #id of question to delete.");

        $question = \App\Models\Question::find($question_id);
        if ($question) {
            $question->delete();
            $this->call('qanda:interactive');
        }  else {
            $this->error('Unknown question choice');
            $this->call('question:delete'); //
        }
    }
}
