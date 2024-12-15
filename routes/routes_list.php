<?php
// This would explain the routes i've created so far as a simple documentation
return [

    [
        'method' => 'GET',
        'uri' => 'api/todos',
        'msg' => 'Get all todos'
    ],
    [
        'method' => 'GET',
        'uri' => 'api/todos/status/{status_type}',
        'msg' => 'Filter todos by status [0-Not Started, 1-In Progress, 2-Completed]'
    ],
    [
        'method' => 'GET',
        'uri' => 'api/todos?search=[search_param]',
        'msg' => 'Search for todo(s) by title and details'
    ],
    [
        'method' => 'GET',
        'uri' => 'api/todos?order=[order_type]',
        'msg' => 'Sorts todos by order [asc, desc] and is an optional query parameter that can be passed to all necessary endpoints'
    ],
    [
        'method' => 'PUT',
        'uri' => 'api/todos/edit/{todo_id}',
        'msg' => 'Edits an existing todo [title, details are optional but status is required'
    ],
    [
        'method' => 'DELETE',
        'uri' => 'api/todos/delete/{todo_id}',
        'msg' => 'Deletes an existing todo'
    ],
    [
        'method' => 'POST',
        'uri' => '/api/todos/new',
        'msg' => 'Create a new todo',
    ]
];