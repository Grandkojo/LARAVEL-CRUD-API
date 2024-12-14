<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class APIRoutesTest extends TestCase
{
    use RefreshDatabase;
    /**
     * test the api/todos/ route
     */
    public function test_todos()
    {
        $response = $this->get('api/todos/');
        $response->assertStatus(200);
    }

    /**
     * test the api/todos/new post route
     */
    public function test_todos_new()
    {
        $mockData = Todo::factory()->make()->toArray();
        $response = $this->postJson('api/todos/new', $mockData);
        $response->assertStatus(201);
    }

    /**
     * test the api/todos/status route
     */
    public function test_todos_status()
    {
        $response = $this->get('api/todos/status');
        $response->assertStatus(200);
    }

    /**
     * test the api/todos/status/{status_type} route
     */
    public function test_todos_status_type_valid()
    {
        Todo::create(['title' => 'First Todo', 'details' => 'Details for first todo', 'status' => '0']);
        Todo::create(['title' => 'Second Todo', 'details' => 'Details for second todo', 'status' => '1']);
        Todo::create(['title' => 'Third Todo', 'details' => 'Details for third todo', 'status' => '2']);

        // Example status type you want to test (e.g., '0', '1', or '2')
        $statusType = 0;
        $response = $this->get("api/todos/status/{$statusType}");
        $response->assertStatus(200);

        $statusType = 1;
        $response = $this->get("api/todos/status/{$statusType}");
        $response->assertStatus(200);

        $statusType = 2;
        $response = $this->get("api/todos/status/{$statusType}");
        $response->assertStatus(200);
    }



    /**
     * test the api/todos/status/{status_type} route with wrong status type
     */
    public function test_todos_wrong_status_type()
    {
        // wrong status type
        $statusType = 5;

        $response = $this->get("api/todos/status/" . $statusType);
        $response->assertStatus(422)
            ->assertJson([
                'status_code' => '422',
                'message' => 'Status can only be: 0-Not Started ,1-In Progress, 2-Completed'
            ]);
    }



    /**
     * test the api/todos/edit/{id} route for existing id
     */
    public function test_todos_edit_existing()
    {
        $todo = Todo::factory()->create();

        $updatedData = [
            'title' => 'This new title',
            'details' => 'Updated Details For myself',
            'status' => '2'
        ];
        $response = $this->putJson('api/todos/edit/' . $todo->id, $updatedData);
        $response->assertStatus(200)
            ->assertJson([
                'status_code' => '200',
                'message' => 'Todo updated successfully',
                'data' => $updatedData,
            ]);
    }

    /**
     * test the api/todos/edit/{id} route for non existing id
     */
    public function test_todos_edit_not_existing()
    {
        $todoId = 99999;

        $updatedData = [
            'title' => 'This new title',
            'details' => 'Updated Details For myself',
            'status' => '2'
        ];
        $response = $this->putJson('api/todos/edit/' . $todoId, $updatedData);
        $response->assertStatus(404)
            ->assertJson([
                'status_code' => '404',
                'message' => 'Todo not found',
            ]);
    }

    /**
     * test the api/todos/delete/{id} route for  existing id
     */
    public function test_todos_delete_existing()
    {
        $todo = Todo::factory()->create();

        $response = $this->deleteJson('api/todos/delete/' . $todo->id);
        $response->assertStatus(200)
            ->assertJson([
                'status_code' => '200',
                'message' => 'Todo deleted successfully'
            ]);
    }

    /**
     * test the api/todos/delete/{id} route for non existing id
     */
    public function test_todos_delete_not_existing()
    {
        $todoId = 99999;

        $response = $this->deleteJson('api/todos/delete/' . $todoId);
        $response->assertStatus(404)
            ->assertJson([
                'status_code' => '404',
                'message' => 'Todo not found',
            ]);
    }
}
