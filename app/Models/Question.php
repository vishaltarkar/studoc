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

    protected $fillable = ['question', 'answer_id'];

    # relationship
    public function result()
    {
        return $this->hasOne(QuestionResult::class);
    }

    # get answer of the question
    public function answer()
    {
        return $this->belongsTo(QuestionOption::class);
    }

    # get answer options of the questions
    public function options()
    {
        return $this->hasMany(QuestionOption::class)->inRandomOrder();
    }

    # custom function
    /**
     * Get Question result data
     *
     * @return array {
     *    'list': array {'id' :int, 'question':string, 'result':string},
     *    'total':int,
     *    'attempt_perc':float,
     *    'correct_perc':float,
     *    'correct_count':int,
     * }
     */
    public static function getResults($user_id = null)
    {
        $correct_count = $attempt_count = 0;
        $questions = Question::with(['result'])->get();
        $list = collect($questions)->map(function ($question) use ($correct_count, $attempt_count) {
            $resultStr = self::NOTANSWERED_TXT;
            if (@$question->result) {
                $attempt_count++;
                if ($question->result->is_correct === 1) {
                    $resultStr = self::CORRECT_TXT;
                    $correct_count++;
                } else {
                    $resultStr = self::INCORRECT_TXT;
                }
            }

            return [
                '#Id' => $question->id,
                'question' => $question->question,
                'result' => $resultStr
            ];
        });
        $question_count = sizeof($questions); // get total no. of questions

        // process corret to %
        $percentage = '0';
        if ($correct_count > 0) {
            $percentage = ($correct_count/$question_count) * 100;
        }

        // attempt question %
        $attempt_percentage = '0';
        if ($attempt_count > 0) {
            $attempt_percentage = ($attempt_count/$question_count) * 100;
        }

        return [
            'list' => $list,
            'total' => $question_count,
            'attempt_perc' => round($attempt_percentage, 0, PHP_ROUND_HALF_DOWN),
            'correct_perc' => round($percentage, 0, PHP_ROUND_HALF_DOWN),
            'correct_count' => $correct_count
        ];
    }
}
