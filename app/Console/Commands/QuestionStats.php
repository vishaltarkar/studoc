<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class QuestionStats extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'question:stats';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Get Question Stats';

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
        $stats = \App\Models\Question::getStatsData();
        $this->newLine(1);
        $this->info("The total questions : " . $stats['total']);
        $this->info("Pecentage of questions that have an answer. : " . round($stats['attempt_perc'], 2) . "%");
        $this->info("Pecentage of questions that have a correct answer. : " . round($stats['correct_perc'], 2). "%");
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
