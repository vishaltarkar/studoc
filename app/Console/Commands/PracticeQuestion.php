<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PracticeQuestion extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'question:practice';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Start Practicing the questions';

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
        // welcome message
        $this->info('>> Welcome to Practice <<');

        $this->comment("Here is the list of question with result");

        // $this->startPractice();
        # confirmation before starting the practice
        // $questions = \App\Models\Question::with(['answer'])->get();
        $this->questionStatusTable();

    }

    private function questionStatusTable()
    {
        $result = $this->getQuestionStatusList();
        $this->table(
            ['#', 'Question', 'Status'],
            $result['list']
        );

        $this->newLine();

        $this->info('Completion in percentage ' . round($result['percentage'], 2) . '%');

        $this->newLine();

        // select the question to answer
        $question_id = $this->selectQuestion();

        $this->answeringQuestion($question_id);

        $this->practiceChoices();
    }

    private function practiceChoices()
    {
        $this->newLine();
        $choice = $this->choice(">> Please select any one of the option below << ", [
            "next" => "Select new question to answer",
            "back" => "Go back",
            "exit" => "Exit"
        ]);

        switch ($choice) {
            case 'next':
                $this->call('question:practice');
                break;

            case 'back':
                $this->call('qanda:interactive');
                break;

            case 'exit':
                $this->info("Good Bye!");
                return 0;
                break;

            default:
                $this->error('Error Msg : Unknown choice');
                $this->call('qanda:practice');
                break;
        }

        $this->newLine();
    }

    private function getQuestionStatusList()
    {
        $list = [];
        $correctCount = 0;
        $questions = \App\Models\Question::with(['result'])->get();
        $questionCount = sizeof($questions);
        if ($questionCount > 0) {
            foreach ($questions as $key => $question) {

                $resultStr = 'Not answered';
                if (@$question->result) {
                    if ($question->result->is_correct === 1) {
                        $resultStr = 'Correct';
                        $correctCount++;
                    } else {
                        $resultStr = 'Incorrect';
                    }
                }

                $list[] = [
                    '#Id' => $question->id,
                    'question' => $question->question,
                    'result' => $resultStr
                ];
            }
        }

        // process corret to %
        $percentage = '0';
        if ($correctCount > 0) {
            $percentage = ($correctCount/$questionCount) * 100;
        }
        return ['list' => $list, 'percentage' => $percentage];
    }

    private function selectQuestion()
    {
        $this->newLine(2);
        $question_id = $this->ask("Select the #Id of the question to answer it.");
        $question = \App\Models\Question::with(['result'])->findOrFail($question_id);
        if ($question) {
            if (!$question->result || $question->result->is_correct === 1) {
                return $question;
            } else {
                $this->info('Question is already answered.');
                $this->selectQuestion();
            }
        } else {
            $this->error('Unknown Question choice.');
        }
    }

    public function answeringQuestion($question) {
        $this->newLine(1);
        $this->info("Here is the question :- ");
        $user_answer = $this->ask($question->question);

        // blank or null answer
        if (!$user_answer || $user_answer === '') {
            $this->error("invalid entry! try again.");
            $this->answeringQuestion($question);
        }


        // check answer
        if (\Illuminate\Support\Str::lower($user_answer) == \Illuminate\Support\Str::lower($question->answer->answer)) {
            $this->comment('<Correct>');
            $is_correct = 1;
        } else {
            $this->error('<Incorrect>');
            $is_correct = 0;
        }

        // store user answer
        \App\Models\QuestionResult::updateOrCreate([
            'question_id' => $question->id,
        ], [
            'answer_value' => $user_answer,
            'is_correct' => $is_correct
        ]);

        $this->newLine(1);
    }
}
