<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Support\Collection;
use Illuminate\Foundation\Testing\RefreshDatabase;

class QuestionTest extends TestCase
{
    use RefreshDatabase;

    /** @test*/
    public function question_result_method_response_type_is_array()
    {
        $result = \App\Models\Question::getResults();
        $this->assertIsArray($result);
    }

    /** @test*/
    public function question_result_method_response_keys_exist()
    {
        $result = \App\Models\Question::getResults();
        $this->assertArrayHasKey('list', $result);
        $this->assertArrayHasKey('total', $result,);
        $this->assertArrayHasKey('correct_count', $result);
        $this->assertArrayHasKey('attempt_perc', $result);
        $this->assertArrayHasKey('correct_perc', $result);
    }

    /** @test*/
    public function question_result_method_response_array_keys_types_validate()
    {
        $result = \App\Models\Question::getResults();

        $this->assertTrue($result['list'] instanceof Collection);
        $this->assertIsInt($result['total']);
        $this->assertIsInt($result['correct_count']);
        $this->assertIsFloat($result['attempt_perc']);
        $this->assertIsFloat($result['correct_perc']);
    }
}
