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
     * Get Question result data
     *
     * @return array
     */
    public static function getResults($user_id = null)
    {
        $list = [];
        $correctCount = $attemptCount = 0;

        $questions = Question::with(['result']);

        if ($user_id) {
            $questions->whereHas('result', function ($q) use ($user_id) {
                $q->OfUser('user_id', $user_id);
            });
        }
        $questions = $questions->get();

        $questionCount = sizeof($questions);
        if ($questionCount > 0) {
            foreach ($questions as $question) {
                $resultStr = self::NOTANSWERED_TXT;
                if (@$question->result) {
                    $attemptCount++;
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

        // attempt question %
        $attemptPercentage = '0';
        if ($attemptCount > 0) {
            $attemptPercentage = ($attemptCount/$questionCount) * 100;
        }

        return [
            'list' => $list,
            'total' => $questionCount,
            'attempt_perc' => round($attemptPercentage, 0, PHP_ROUND_HALF_DOWN),
            'correct_perc' => round($percentage, 0, PHP_ROUND_HALF_DOWN),
            'correct_count' => $correctCount
        ];
    }
}
