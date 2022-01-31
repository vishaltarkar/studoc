<?php

namespace Tests\Unit;

use App\Console\Commands\CreateQuestion;
use Tests\TestCase;
use App\Models\Question;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;

class QuestionTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    /** @test */
    public function question_result_method_exist()
    {
        $exist = method_exists(Question::class, 'getResults');
        $this->assertTrue($exist, "getResults Method doesnt exist in Question Class.");
    }


    /** @test*/
    public function question_result_method_response_type_is_array()
    {
        $result = Question::getResults();
        $this->assertIsArray($result, "Question::getResults() method should return an Array");
    }

    /** @test*/
    public function question_result_method_response_keys_exist()
    {
        $result = Question::getResults();
        $this->assertArrayHasKey('list', $result, "`list` key is missing.");
        $this->assertArrayHasKey('total', $result, "`total` key is missing.");
        $this->assertArrayHasKey('correct_count', $result, "`correct_count` key is missing.");
        $this->assertArrayHasKey('attempt_perc', $result, "`attempt_perc` key is missing.");
        $this->assertArrayHasKey('correct_perc', $result, "`correct_perc` key is missing.");
    }

    /** @test*/
    public function question_result_method_response_array_keys_types_validate()
    {
        $result = Question::getResults();

        $this->assertTrue($result['list'] instanceof Collection, "`list` should be a collection.");
        $this->assertIsInt($result['total'], "`total` should be an Integer");
        $this->assertIsInt($result['correct_count'], "`correct_count` should be an Integer");
        $this->assertIsFloat($result['attempt_perc'], "`attempt_perc` should be exist");
        $this->assertIsFloat($result['correct_perc'], "`correct_perc` should be exist");
    }
}
