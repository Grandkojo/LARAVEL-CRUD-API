<?php

use Tests\TestCase;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoControllerIndexTest extends TestCase
{
    use RefreshDatabase;

    public function test_index_returns_todos_in_desc_order_by_default()
    {
        // Create some todos
        $todos = Todo::factory()->count(5)->create();

        // Call the API
        $response = $this->getJson('/api/todos');

        // Assert response
        $response->assertStatus(200)
            ->assertJsonPath('status_code', '200')
            ->assertJsonCount(5, 'todos.data'); 
    }

    public function test_index_returns_422_for_invalid_order_parameter()
    {
        $response = $this->getJson('/api/todos?order=random');

        $response->assertStatus(422)
            ->assertJsonPath('status_code', '422')
            ->assertJsonPath('message', 'Order type can only be asc or desc');
    }

    public function test_index_returns_todos_in_asc_order()
    {
        $todos = Todo::factory()->count(5)->create();

        $response = $this->getJson('/api/todos?order=asc');

        $response->assertStatus(200)
            ->assertJsonPath('status_code', '200')
            ->assertJsonCount(5, 'todos.data');
    }

    public function test_index_returns_422_for_short_search_param()
    {
        $response = $this->getJson('/api/todos?search=a');

        $response->assertStatus(422)
            ->assertJsonPath('status_code', '422')
            ->assertJsonPath('message', 'Search parameter should be 2 or more characters long');
    }

    public function test_index_returns_todos_for_valid_search_param()
    {
        Todo::factory()->create(['title' => 'Learn Laravel']);
        Todo::factory()->create(['title' => 'Practice PHP']);

        $response = $this->getJson('/api/todos?search=Laravel');

        $response->assertStatus(200)
            ->assertJsonPath('status_code', '200')
            ->assertJsonCount(1, 'data.data'); 
    }

    public function test_index_returns_404_for_search_with_no_results()
    {
        Todo::factory()->create(['title' => 'Learn Laravel']);

        $response = $this->getJson('/api/todos?search=React');

        $response->assertStatus(404)
            ->assertJsonPath('status_code', '404')
            ->assertJsonPath('message', 'Todo(s) cannot be found. Try a different search');
    }
}
