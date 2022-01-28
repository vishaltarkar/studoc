<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QuestionResult extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'question_id', 'answer_id', 'is_correct'];

    #relationship
    public function question()
    {
        return $this->belongsTo(Question::class);
    }

    public function answer()
    {
        return $this->belongsTo(QuestionOption::class);
    }

    #scope
    public function scopeOfUserId($query, $user_id)
    {
        return $query->where('user_id', $user_id);
    }
}
