<?php

use Tests\TestCase;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoControllerFilterStatusTest extends TestCase
{
    use RefreshDatabase;

    public function test_filter_by_status_returns_todos_with_valid_status_and_default_order()
    {
        // Create todos with different statuses
        Todo::factory()->create(['status' => 0]); // Not Started
        Todo::factory()->create(['status' => 1]); // In Progress

        $response = $this->getJson('/api/todos/status/0');

        $response->assertStatus(200)
            ->assertJsonPath('status_code', '200')
            ->assertJsonCount(1, 'data.data'); // Assuming 'data' is paginated
    }

    public function test_filter_by_status_returns_422_for_invalid_status_type()
    {
        $response = $this->getJson('/api/todos/status/invalid');

        $response->assertStatus(422)
            ->assertJsonPath('status_code', '422')
            ->assertJsonPath(
                'message',
                'Status can only be: 0-Not Started ,1-In Progress, 2-Completed'
            );
    }

    public function test_filter_by_status_returns_200_for_missing_status_type()
    {
        $response = $this->getJson('/api/todos/status/');

        $response->assertStatus(200)
            ->assertJsonPath('status_code', '200')
            ->assertJsonPath(
                'message',
                'Provide a status to filter by after the endpoint - 0-Not Started ,1-In Progress, 2-Completed'
            );
    }

    public function test_filter_by_status_returns_422_for_invalid_order_parameter()
    {
        $response = $this->getJson('/api/todos/status/1?order=random');

        $response->assertStatus(422)
            ->assertJsonPath('status_code', '422')
            ->assertJsonPath('message', 'Order type can only be asc or desc');
    }

    public function test_filter_by_status_returns_422_for_short_search_param()
    {
        $response = $this->getJson('/api/todos/status/1?search=a');

        $response->assertStatus(422)
            ->assertJsonPath('status_code', '422')
            ->assertJsonPath(
                'message',
                'Search parameter should be 2 or more characters long'
            );
    }

    public function test_filter_by_status_returns_todos_with_valid_search_param()
    {
        Todo::factory()->create(['status' => 1, 'title' => 'Learn Laravel']);
        Todo::factory()->create(['status' => 1, 'details' => 'Practice PHP']);

        $response = $this->getJson('/api/todos/status/1?search=Laravel');

        $response->assertStatus(200)
            ->assertJsonPath('status_code', '200')
            ->assertJsonCount(1, 'data.data'); 
    }

    public function test_filter_by_status_returns_404_for_search_with_no_results()
    {
        Todo::factory()->create(['status' => 1, 'title' => 'Learn Laravel']);

        $response = $this->getJson('/api/todos/status/1?search=React');

        $response->assertStatus(404)
            ->assertJsonPath('status_code', '404')
            ->assertJsonPath(
                'message',
                'Todo(s) cannot be found. Try a different search'
            );
    }
}
