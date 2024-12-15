<?php

namespace Tests\Unit;

use Tests\TestCase;

class RoutesListTest extends TestCase
{
    public function test_routes_list_returns_correct_data()
    {
        // Mock the `routes/routes_list.php` file to return sample data
        $mockRoutes = [
    
            [
                'method' => 'POST',
                'uri' => '/api/todos/new',
                'msg' => 'Create a new todo',
            ],
        ];

        // Use Laravel's filesystem helper to mock the file content
        $this->mockFilesystem($mockRoutes);

        // Make a request to the routes_list endpoint
        $response = $this->getJson('/api/routes-list');

        // Assert that the response is structured correctly
        $response->assertStatus(200);
        $response->assertJsonStructure([
            'idx',
            'status_code',
            'routes' => [
                '*' => [
                    'method',
                    'uri',
                    'message',
                ],
            ],
        ]);

        // Assert that the routes data matches the mock data
    

        $response->assertJsonFragment([
            'method' => 'POST',
            'uri' => '/api/todos/new',
            'message' => 'Create a new todo',
        ]);
    }

    /**
     * Helper method to mock the filesystem for the `routes/routes_list.php` file.
     */
    protected function mockFilesystem($mockRoutes)
    {
        $this->mock('files', function ($mock) use ($mockRoutes) {
            $mock->shouldReceive('require')
                ->with(base_path('routes/routes_list.php'))
                ->andReturn($mockRoutes);
        });
    }
}
