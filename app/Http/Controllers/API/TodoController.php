<?php

namespace App\Http\Controllers\API;

use App\Models\Todo;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

use function PHPSTORM_META\type;
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
        $data = [];

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

        $todos = Todo::orderBy('created_at', $order)->where('status', $status_type)->paginate(10);

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
}
