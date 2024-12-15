<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoDestroyTest extends TestCase
{
    use RefreshDatabase;
    public function test_destroy_fails_when_todo_not_found()
    {
        $invalidTodoId = 999; 

        // Send a request to delete a Todo that does not exist
        $response = $this->deleteJson('/api/todos/delete/' . $invalidTodoId);

        // Assert the status code is 404
        $response->assertStatus(404);

        $response->assertJson([
            'status_code' => '404',
            'message' => 'Todo not found',
        ]);
    }

    public function test_destroy_succeeds_when_todo_exists()
    {
        $todo = Todo::factory()->create();

        // Send a request to delete the Todo
        $response = $this->deleteJson('/api/todos/delete/' . $todo->id);

        // Assert the status code is 200
        $response->assertStatus(200);

        $response->assertJson([
            'status_code' => '200',
            'message' => 'Todo deleted successfully',
        ]);

        $this->assertNull(Todo::find($todo->id)); 
    }
}
