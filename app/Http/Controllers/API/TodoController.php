<?php

namespace App\Http\Controllers\API;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

class TodoController extends Controller
{

    const orderType = ['asc', 'desc'];
    const statusTypes = ['0', '1', '2'];
    /**
     * Get all todos.
     * 
     * Retrieves a list of todos with optional ordering and search parameters.
     * 
     * @group Todos
     * 
     * @queryParam order string Order of the results. Can be `asc` or `desc`. Default is `desc`. Example: "desc".
     * @queryParam search string Search term for filtering todos by title or details. Must be at least 2 characters. Example: "meeting".
     * 
     * @response 200 {
     *   "status_code": 200,
     *   "todos": []
     * }
     * @response 422 {
     *   "status_code": 422,
     *   "message": "Order type can only be asc or desc"
     * }
     */

    public function index(Request $request)
    {
        $order = $request->query('order', 'desc');
        $search_param = $request->query('search', '');
        $data = [];

        if ($search_param == null) {

            if (!in_array($order, self::orderType)) {

                $data = [
                    'status_code' => '422',
                    'message' => 'Order type can only be asc or desc'
                ];

                return response()->json($data, 422);
            }

            $todos = Todo::orderBy('created_at', $order)->paginate(10);

            $data = [
                'status_code' => '200',
                'todos' => $todos
            ];

            return response()->json($data, 200);
        } elseif (strlen($search_param) < 2) {

            $data = [
                'status_code' => '422',
                'message' => 'Search parameter should be 2 or more characters long'
            ];

            return response()->json($data, 422);
        }




        $todos = Todo::orderBy('created_at', $order)
            ->where('title', 'like', '%' . $search_param . '%')
            ->orWhere('details', 'like', '%' . $search_param . '%')
            ->paginate(10);


        if ($todos->isEmpty()) {

            $data = [
                'status_code' => '404',
                'message' => 'Todo(s) cannot be found. Try a different search'
            ];
            return response()->json($data, 404);
        } else {

            $data = [
                'status_code' => '200',
                'data' => $todos
            ];

            return response()->json($data, 200);
        }
    }

    /**
     * Filter todos by status.
     * 
     * Filters todos based on their status and optional search parameters.
     * 
     * @group Todos
     * 
     * @queryParam order string Order of the results. Can be `asc` or `desc`. Default is `desc`. Example: "asc".
     * @queryParam search string Search term for filtering todos by title or details. Must be at least 2 characters. Example: "meeting".
     * @urlParam status_type int required Status type to filter by (0=Not Started, 1=In Progress, 2=Completed). Example: 1
     * 
     * @response 200 {
     *   "status_code": 200,
     *   "data": []
     * }
     * @response 404 {
     *   "status_code": 404,
     *   "message": "Todo(s) cannot be found. Try a different search"
     * }
     */
    public function filter_by_status(Request $request, $status_type)
    {
        $order = $request->query('order', 'desc');
        $search_param = $request->query('search', '');
        $data = [];

        if ($search_param == null) {


            if (!in_array($status_type, self::statusTypes)) {
                $data = [
                    'status_code' => '422',
                    'message' => 'Status can only be: 0-Not Started ,1-In Progress, 2-Completed'
                ];
                return response()->json($data, 422);
            }


            if (!in_array($order, self::orderType)) {

                $data = [
                    'status_code' => '422',
                    'message' => 'Order type can only be asc or desc'
                ];

                return response()->json($data, 422);
            }
        } elseif (strlen($search_param) < 2) {

            $data = [
                'status_code' => '422',
                'message' => 'Search parameter should be 2 or more characters long'
            ];

            return response()->json($data, 422);
        }



        $todos = Todo::orderBy('created_at', $order)
            ->where('status', $status_type)
            ->where(function ($query) use ($search_param) {
                $query->orWhere('title', 'like', '%' . $search_param . '%')
                    ->orWhere('details', 'like', '%' . $search_param . '%');
            })
            ->paginate(10);


        if ($todos->isEmpty()) {

            $data = [
                'status_code' => '404',
                'message' => 'Todo(s) cannot be found. Try a different search'
            ];
            return response()->json($data, 404);
        } else {

            $data = [
                'status_code' => '200',
                'data' => $todos
            ];
        }

        return response()->json($data, 200);
    }

    /**
     * Display filter guidance.
     * 
     * Provides instructions on filtering todos by status.
     * 
     * @group Todos
     * 
     * @response 200 {
     *   "status_code": 200,
     *   "message": "Provide a status to filter by after the endpoint - 0=Not Started, 1=In Progress, 2=Completed"
     * }
     */
    public function filter_message()
    {
        $data = [
            'status_code' => '200',
            'message' => 'Provide a status to filter by after the endpoint - 0-Not Started ,1-In Progress, 2-Completed'
        ];
        return response()->json($data, 200);
    }

    /**
     * Get the list of routes.
     * 
     * Returns a list of available API routes.
     * 
     * @group Misc
     * 
     * @response 200 {
     *   "idx": "Welcome to Laravel Crud API",
     *   "status_code": 200,
     *   "routes": []
     * }
     */
    public function routes_list()
    {
        $routes = require base_path('routes/routes_list.php');
        $routesArray = [];

        foreach ($routes as $route) {
            $routesArray[] = [
                'method' => $route['method'],
                'uri' => $route['uri'],
                'message' => $route['msg']
            ];
        }

        $data = [
            'idx' => 'Welcome to Laravel Crud API',
            'status_code' => '200',
            'routes' => $routesArray,
        ];
        return response()->json($data, 200);
    }

    /**
     * Create a new todo.
     * 
     * Adds a new todo to the database.
     * 
     * @group Todos
     * 
     * @bodyParam title string required The title of the todo. Example: "Finish project".
     * @bodyParam details string required Details about the todo. Must be at least 10 characters. Example: "Complete all tasks for the project".
     * @bodyParam status int required The status of the todo. Must be 0, 1, or 2. Example: 0
     * 
     * @response 201 {
     *   "status_code": 201,
     *   "message": "Todo created successfully"
     * }
     * @response 422 {
     *   "status_code": 422,
     *   "message": {
     *     "title": ["The title field is required."]
     *   }
     * }
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['required', 'string', Rule::unique('todos', 'title')],
            'details' => ['required', 'string', 'min:10'],
            'status' => ['required', Rule::in(['0', '1', '2'])]

        ]);

        if ($validator->fails()) {
            $data = [
                'status_code' => '422',
                'message' => $validator->messages()
            ];
            return response()->json($data, 422);
        } else {

            $todo = new Todo;

            $todo->title = $request->title;
            $todo->details = $request->details;
            $todo->status = $request->status;

            $todo->save();

            $data = [
                'status_code' => '201',
                'message' => 'Todo created successfully',
            ];

            return response()->json($data, 201);
        }
    }

    /**
     * Update an existing todo.
     * 
     * Modifies an existing todo in the database. The id should be a number(it uses string as a decor)
     * 
     * @group Todos
     * 
     * @bodyParam title string The new title for the todo. Example: "New Title".
     * @bodyParam details string The new details for the todo. Example: "Updated details for the todo".
     * @bodyParam status int The new status for the todo. Must be 0, 1, or 2. Example: 2
     * 
     * @response 200 {
     *   "status_code": 200,
     *   "message": "Todo updated successfully",
     *   "data": {}
     * }
     * @response 404 {
     *   "status_code": 404,
     *   "message": "Todo not found"
     * }
     */
    public function update(Request $request, $todo_id)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['string', Rule::unique('todos', 'title')],
            'details' => ['string', 'min:10'],
            'status' => [Rule::in(['0', '1', '2'])]

        ]);

        if ($validator->fails()) {
            $data = [
                'status_code' => '422',
                'message' => $validator->messages()
            ];
            return response()->json($data, 422);
        } else {

            $todo = Todo::find($todo_id);

            if (!$todo) {
                $data = [
                    'status_code' => '404',
                    'message' => 'Todo not found',
                ];
                return response()->json($data, 404);
            }

            $todo->update($request->only(['title', 'details', 'status']));


            $data = [

                'status_code' => '200',
                'message' => 'Todo updated successfully',
                'data' => $todo,
            ];


            return response()->json($data, 200);
        }
    }

    /**
     * Delete a todo.
     * 
     * Removes a todo from the database. The id should be a number(it uses string as a decor)
     * 
     * @group Todos
     * 
     * 
     * @response 200 {
     *   "status_code": 200,
     *   "message": "Todo deleted successfully"
     * }
     * @response 404 {
     *   "status_code": 404,
     *   "message": "Todo not found"
     * }
     */
    public function destroy($todo_id)
    {
        $todo = Todo::find($todo_id);

        if (!$todo) {
            $data = [
                'status_code' => '404',
                'message' => 'Todo not found',
            ];
            return response()->json($data, 404);
        } else {
            $todo->delete();

            $data = [
                'status_code' => '200',
                'message' => 'Todo deleted successfully'
            ];
            return response()->json($data, 200);
        }
    }
}
