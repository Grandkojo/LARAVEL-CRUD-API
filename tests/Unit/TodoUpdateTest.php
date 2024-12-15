<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoUpdateTest extends TestCase
{
    use RefreshDatabase;
    public function test_update_with_missing_fields()
    {
        $todo = Todo::factory()->create();

        $response = $this->putJson('/api/todos/edit/' . $todo->id, []);


        $response->assertStatus(200);

        $response->assertJsonFragment([
            'status_code' => '200',
            'message' => 'Todo updated successfully'
        ]);
    }

    public function test_update_fails_with_short_details_validation_errors()
    {
        $todo = Todo::factory()->create(); // Assuming you have a factory to create a Todo

        $response = $this->putJson('/api/todos/edit/' . $todo->id, ["details" => "Hello"]);


        $response->assertStatus(422);

        $response->assertJson([
            'status_code' => '422',
            'message' => [
                'details' => [
                    0 => 'The details field must be at least 10 characters.'
                ]
            ]
        ]);
    }

    public function test_update_fails_with_existing_title_validation_errors()
    {
        $todo = Todo::factory()->create(['title' => 'Learn Laravel']);

        $response = $this->putJson('/api/todos/edit/' . $todo->id, ["title" => "Learn Laravel"]);


        $response->assertStatus(422);

        $response->assertJson([
            'status_code' => '422',
            'message' => [
                'title' => [
                    0 => 'The title has already been taken.'
                ]
            ]
        ]);
    }

    public function test_update_fails_with_wrong_status_validation_errors()
    {
        $todo = Todo::factory()->create();

        $response = $this->putJson('/api/todos/edit/' . $todo->id, ["status" => "5"]);


        $response->assertStatus(422);

        $response->assertJson([
            'status_code' => '422',
            'message' => [
                'status' => [
                    0 => 'The selected status is invalid.'
                ]
            ]
        ]);
    }


    public function test_update_fails_when_todo_not_found()
    {
        $invalidTodoId = 99329;

        $response = $this->putJson('/api/todos/edit/' . $invalidTodoId, [
            'title' => 'Updated title',
            'details' => 'Updated details for the todo',
            'status' => '1',
        ]);

        $response->assertStatus(404);

        $response->assertJson([
            'status_code' => '404',
            'message' => 'Todo not found',
        ]);
    }

    public function test_update_succeeds_with_valid_data()
    {
        $todo = Todo::factory()->create();

        // Send a request to update the Todo with valid data
        $response = $this->putJson('/api/todos/edit/' . $todo->id, [
            'title' => 'Updated title',
            'details' => 'Updated details for the todo',
            'status' => '1',
        ]);

        $response->assertStatus(200);

        $response->assertJson([
            'status_code' => '200',
            'message' => 'Todo updated successfully',
        ]);

        $todo->refresh();
        $this->assertEquals('Updated title', $todo->title);
        $this->assertEquals('Updated details for the todo', $todo->details);
        $this->assertEquals('1', $todo->status);
    }
}
