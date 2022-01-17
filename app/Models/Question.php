<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    public const NOTANSWERED_TXT = 'Not answered';
    public const CORRECT_TXT = 'Correct';
    public const INCORRECT_TXT = 'Incorrect';

    protected $fillable = ['question', 'answer'];

    # relationship
    public function result()
    {
        return $this->hasOne(QuestionResult::class);
    }

    # custom function
    /**
     * get Data for the Practice Table
     *
     * @return array
     */
    public static function getPracticeData()
    {
        $list = [];
        $correctCount = 0;
        $questions = Question::with(['result'])->get();
        $questionCount = sizeof($questions);
        if ($questionCount > 0) {
            foreach ($questions as $question) {
                $resultStr = self::NOTANSWERED_TXT;
                if (@$question->result) {
                    if ($question->result->is_correct === 1) {
                        $resultStr = self::CORRECT_TXT;
                        $correctCount++;
                    } else {
                        $resultStr = self::INCORRECT_TXT;
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

    /**
     * Get Data for Stats
     *
     * @return array
     */
    public static function getStatsData()
    {
        $correctCount = $attemptCount = 0;
        $questions = \App\Models\Question::with(['result'])->get();
        $questionCount = sizeof($questions);
        if ($questionCount > 0) {
            foreach ($questions as $key => $question) {

                if (@$question->result) {
                    $attemptCount++;
                    if ($question->result->is_correct === 1) {
                        $correctCount++;
                    }
                }
            }
        }

        // corret answer  %
        $correctPercent = '0';
        if ($correctCount > 0) {
            $correctPercent = ($correctCount/$questionCount) * 100;
        }

        // attempt question %
        $attemptPercentage = '0';
        if ($attemptCount > 0) {
            $attemptPercentage = ($attemptCount/$questionCount) * 100;
        }

        return [
            'total' => $questionCount,
            'correct_perc' => $correctPercent,
            'attempt_perc' => $attemptPercentage
        ];
    }
}
