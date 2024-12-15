<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoStoreTest extends TestCase
{
    use RefreshDatabase;
    /**
     * Test validation fails for missing fields.
     */
    public function test_store_fails_with_missing_fields()
    {
        $response = $this->postJson('/api/todos/new/', []);

        // $response->dump();

        $response->assertStatus(422);
        $response->assertJson([
            'status_code' => '422',
            'message' => [
                'title' => ['The title field is required.'],
                'details' => ['The details field is required.'],
                'status' => ['The status field is required.']
            ]
        ]);
    }

    /**
     * Test validation fails for duplicate title.
     */
    public function test_store_fails_with_duplicate_title()
    {
        Todo::create([
            'title' => 'Duplicate Title',
            'details' => 'This is a valid todo description.',
            'status' => '1',
        ]);

        $response = $this->postJson('/api/todos/new', [
            'title' => 'Duplicate Title',
            'details' => 'Another description.',
            'status' => '0',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status_code' => '422',
            'message' => [
                'title' => ["The title has already been taken."]
            ]
        ]);
    }

    // /**
    //  * Test validation fails for short details.
    //  */
    public function test_store_fails_with_short_details()
    {
        $response = $this->postJson('/api/todos/new', [
            'title' => 'New Todo',
            'details' => 'Short',
            'status' => '1',
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status_code' => '422',
            'message' => [
                'details' => ['The details field must be at least 10 characters.'],
            ]
        ]);
    }

    // /**
    //  * Test validation fails for invalid status.
    //  */
    public function test_store_fails_with_invalid_status()
    {
        $response = $this->postJson('/api/todos/new', [
            'title' => 'New Todo',
            'details' => 'This is a valid todo description.',
            'status' => '3', // Invalid status
        ]);

        $response->assertStatus(422);
        $response->assertJson([
            'status_code' => '422',
            'message' => [
                'status' => ['The selected status is invalid.']
            ]
        ]);
    }

    // /**
    //  * Test successful creation of a todo.
    //  */
    public function test_store_creates_todo_successfully()
    {
        $response = $this->postJson('/api/todos/new', [
            'title' => 'New Todo',
            'details' => 'This is a valid todo description.',
            'status' => '0',
        ]);

        $response->assertStatus(201);
        $response->assertJson([
            'status_code' => '201',
            'message' => 'Todo created successfully',
        ]);

        $this->assertDatabaseHas('todos', [
            'title' => 'New Todo',
            'details' => 'This is a valid todo description.',
            'status' => '0',
        ]);
    }
}
