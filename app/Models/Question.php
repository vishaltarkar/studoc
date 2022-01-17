<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    use HasFactory;

    protected $fillable = ['question'];

    # relationship
    public function answer()
    {
        return $this->hasOne(QuestionAnswer::class);
    }

    public function result()
    {
        return $this->hasOne(QuestionResult::class);
    }
}
