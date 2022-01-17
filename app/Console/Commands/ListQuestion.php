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
        // display question list
        $this->table(
            ['#', 'Question', 'Answer'],
            $this->getQuestionWithAnsList()
        );
        $this->newLine(1);
        $this->call('qanda:interactive');

    }

    // Get list of questions
    private function getQuestionWithAnsList()
    {
        $list = [];
        $questions = \App\Models\Question::with(['answer'])->get();
        if (sizeof($questions) > 0) {
            foreach ($questions as $key => $question) {
                $key++;
                $list[] = [
                    '#' => $key,
                    'question' => $question->question,
                    'answer' => $question->answer->answer ?? "-"
                ];
            }
        }
        return $list;
    }
}
