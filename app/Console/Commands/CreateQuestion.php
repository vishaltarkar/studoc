<?php

namespace App\Console\Commands;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Console\BaseQuestionCommand;

class CreateQuestion extends BaseQuestionCommand
{

    public const ADD_QUESTION_SLUG = 'add';
    public const ADD_QUESTION_TXT = "Add question";

    public const SUB_MENU_TITLE_TXT = "Create Question";

    protected const SUB_MENU = [
        self::ADD_QUESTION_SLUG => self::ADD_QUESTION_TXT,
    ];

    public const CREATE_QUESTION_TXT = "Please enter the question.";
    public const ENTER_OPTION_TXT = "Please enter the multiple options separated by ','";
    public const SELECT_CORRECT_OPTION_TXT = "Please select the correct option";
    public const QUESTION_ADD_SUCCESS_TXT = "Question successfully added.";
    const START_OVER_TXT = "Do you want to start over? Enter `no` to try again.";

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'question:create {action?}';

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
        if ($this->argument('action')) {
            $this->handleMenuChoices($this->argument('action'));
        } else {
            parent::handle();
        }
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
                $question = $this->addQuestion();
                if ($option_array = $this->addOptions()) {
                    $question_created = $this->createQuestionInDatabase($question, $option_array);
                    if (!$question_created) {
                        $this->error("Question & Option creation failed.");
                    }
                }
                $this->call('question:create');
                break;
        }
    }

    /**
     * Function to ask user to add question
     *
     * @return string
     */
    protected function addQuestion(): string
    {
        // prompt user for the question txt
        return $this->promptInputWithValidation(self::CREATE_QUESTION_TXT, 'Question');
    }

    /**
     * Ask user to add options for the question
     *
     * @return array|null
     */
    protected function addOptions()
    {
        // prompt user for the question options
        $option_string = $this->promptInputWithValidation(self::ENTER_OPTION_TXT, 'Option');

        $option_string = chop($option_string, ','); // remove if user enter

        $option_array = preg_split('/, ?/', $option_string);

        $option_array = array_filter($option_array);

        // check if there are not less than
        if (count($option_array) >= 3) {
            return $option_array;
        }
        $this->info("Invalid Option Entry, Try Again!");
        if ($this->confirm("Do you want to start over? Enter `no` to try again.")) {
            return null;
        } else {
            $this->addOptions();
        }
    }

    /**
     * Make Entry of Question and Options to DB
     *
     * @param string $question
     * @param array $options
     * @return void
     */
    protected function createQuestionInDatabase(string $question, array $options)
    {
        try {
            DB::beginTransaction();
            $ques = \App\Models\Question::create(compact('question'));
            collect($options)->each(function ($option, $key) use ($ques) {
                $ques_option = $ques->options()->create(['question_id' => $ques->id, 'option_txt' => $option]);
                if (!$ques->answer_id && $ques_option) {
                    $ques->update(['answer_id' => $ques_option->id]);
                    $ques->refresh();
                }
            });
            DB::commit();
            return true;
        } catch (\Exception $e) {
            DB::rollback();
            $this->error($e->getMessage());
        }
    }
}
