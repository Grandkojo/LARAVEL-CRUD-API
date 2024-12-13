<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaginationAndOptionalParametersTest extends TestCase
{
    use RefreshDatabase;
    /**
     * this checks if the pagination works
     */
    public function test_pagination()
    {
        $response = $this->getJson('/api/todos?page=1');
        $response->assertStatus(200)
            ->assertJsonStructure([
                "todos" => [
                    "data",
                    "links",
                    "first_page_url",
                    "next_page_url",
                    "path"
                ]
            ]);
    }

    /**
     * this test the optional order query parameter
     */

    public function test_order_query_param()
    {
        $todo1 = Todo::create(['title' => 'First Todo', 'details' => 'Details for first todo', 'status' => '1']);
        $todo2 = Todo::create(['title' => 'Second Todo', 'details' => 'Details for second todo', 'status' => '2']);
        $todo3 = Todo::create(['title' => 'Third Todo', 'details' => 'Details for third todo', 'status' => '1']);

        $response = $this->getJson('/api/todos?order=asc');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'title' => 'First Todo'
        ]);
        $response->assertJsonFragment([
            'title' => 'Second Todo'
        ]);
        $response->assertJsonFragment([
            'title' => 'Third Todo'
        ]);

        $response = $this->getJson('/api/todos?order=desc');

        $response->assertStatus(200);

        $response->assertJsonFragment([
            'title' => 'Third Todo'
        ]);
        $response->assertJsonFragment([
            'title' => 'Second Todo'
        ]);
        $response->assertJsonFragment([
            'title' => 'First Todo'
        ]);
    }

    /**
     * this test the search query paramater for todos which is not found
     */

    public function test_todos_search_query_param_not_found()
    {
        // search doesn't find any 
        $search = 'Srfvbsndffv';
        $response = $this->getJson('/api/todos?search=' . $search);
        $response->assertStatus(404)
            ->assertJson([
                'status_code' => '404',
                'message' => 'Todo(s) cannot be found. Try a different search'
            ]);
    }

    /**
     * this test the search query paramater for todos which is found
     */

    public function test_todos_search_query_param_found()
    {
        $todo = Todo::create([
            'title' => 'Attend meeting',
            'details' => 'Attend the scheduled meeting tomorrow',
            'status' => '1'
        ]);

        // search for todo by title and details
        $search = 'attend';

        $response = $this->getJson('/api/todos?search=' . $search);

        $response->assertStatus(200)
            ->assertJsonFragment([
                'title' => $todo->title,
                'details' => $todo->details,
                'status' => $todo->status,
            ]);
    }


    /**
     * this test the search query paramater for todos by status which is not found
     */

     public function test_todos_status_search_query_param_not_found()
     {
         // search doesn't find any 
         $search = 'Strdhgfsfds';
         $status = '1';
         $response = $this->getJson("/api/todos/status/{$status}?search=" . $search);
         $response->assertStatus(404)
             ->assertJson([
                 'status_code' => '404',
                 'message' => 'Todo(s) cannot be found. Try a different search'
             ]);
     }
 
     /**
      * this test the search query paramater for todos by status which is found
      */
 
    //  public function test_todos_status_search_query_param_found()
    //  {
    //      $todo = Todo::create([
    //          'title' => 'Attend meeting',
    //          'details' => 'Attend the scheduled meeting tomorrow',
    //          'status' => '1'
    //      ]);
 
    //      // search for todo by title and details
    //      $search = 'attend';
 
    //      $response = $this->getJson('/api/todos?search=' . $search);
 
    //      $response->assertStatus(200)
    //          ->assertJsonFragment([
    //              'title' => $todo->title,
    //              'details' => $todo->details,
    //              'status' => $todo->status,
    //          ]);
    //  }
}
