<?php

namespace App\Http\Controllers\API;

use App\Models\Todo;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

use function PHPSTORM_META\type;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;

use function PHPUnit\Framework\isEmpty;

class TodoController extends Controller
{
    /**
     * return all todos registered
     * 
     * @param Illuminate\Http\Request $request the get request to retrieve the data
     * 
     * @return Illuminate\Http\Response the response indicating the result of the operation
     */

    const orderType = ['asc', 'desc'];
    const statusTypes = ['0', '1', '2'];

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

        if (!in_array($order, self::orderType)) {

            $data = [
                'status_code' => '422',
                'message' => 'Order type can only be asc or desc'
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
     * return all todos registered
     * 
     * @param Illuminate\Http\Request $request the get request to retrieve the data
     * @param int $status_type the status type to filter todos
     * @var array<List> the data to be stored before returned
     * 
     * @return Illuminate\Http\Response the response indicating the result of the operation
     */

    public function filter_by_status(Request $request, $status_type)
    {

        $order = $request->query('order', 'desc');
        $search_param = $request->query('search', '');
        $data = [];

        if ($search_param == null) {

            if ($status_type != null) {

                if (!in_array($status_type, self::statusTypes)) {
                    $data = [
                        'status_code' => '422',
                        'message' => 'Status can only be: 0-Not Started ,1-In Progress, 2-Completed'
                    ];
                    return response()->json($data, 422);
                }
            } else {
                $data = [
                    'status_code' => '422',
                    'message' => 'Please provide a status type. 0-Not Started ,1-In Progress, 2-Completed'
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

        if (!in_array($order, self::orderType)) {

            $data = [
                'status_code' => '422',
                'message' => 'Order type can only be asc or desc'
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


        $data = [
            'status_code' => '200',
            'data' => $todos
        ];
        return response()->json($data, 200);
    }


    /**
     * Returns the guide on how to filter the todos by status
     * @return Illuminate\Http\Response the response indicating the result of the operation 
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
     * returns the list of routes available
     * @return array<List>
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
     * creates a new todo in the database
     * @param Illuminate\Http\Request $request the post request to create the new todo
     * @return Illuminate\Http\Response the response indicating the result of the operation 
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
                'status_code' => 200,
                'message' => 'Todo created successfully',
            ];

            return response()->json($data, 200);
        }
    }


    /**
     * edits an existing todo in the database
     * @param Illuminate\Http\Request $request the post request to create the new todo
     * @param int $todo_id id of the todo
     * @return Illuminate\Http\Response the response indicating the result of the operation 
     */
    public function update(Request $request, $todo_id)
    {
        $validator = Validator::make($request->all(), [
            'title' => ['string', Rule::unique('todos', 'title')],
            'details' => ['string', 'min:10'],
            'status' => ['required', Rule::in(['0', '1', '2'])]

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
                    'status_code' => 404,
                    'message' => 'Todo not found',
                ];
                return response()->json($data, 404);
            }

            $todo->update($request->only(['title', 'details', 'status']));


            $data = [

                'status_code' => 200,
                'message' => 'Todo updated successfully',
                'data' => $todo,
            ];


            return response()->json($data, 200);
        }
    }

    /**
     * deletes an existing todo
     * @param int $todo_id id of the todo
     * @return Illuminate\Http\Response the response indicating the result of the operation 
     */

    public function destroy($todo_id)
    {
        $todo = Todo::find($todo_id);

        if (!$todo) {
            $data = [
                'status_code' => 404,
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
