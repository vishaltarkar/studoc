<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionResult extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'question_id', 'answer_value', 'is_correct'];

    #relationship
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

}
