<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class ResetApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'question:reset-result';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Reset All the previously practice result for the questions.';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected const REST_TXT = "Are you sure want to delete all practice result?";
    protected const REST_SUCCESS_MSG = "Practice result has been reset.";

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        if ($this->confirm(self::REST_TXT)) {
            \Illuminate\Support\Facades\Schema::disableForeignKeyConstraints();

            \App\Models\QuestionResult::truncate(); // truncate results

            \Illuminate\Support\Facades\Schema::enableForeignKeyConstraints();
            $this->info(self::REST_SUCCESS_MSG);
        }
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
