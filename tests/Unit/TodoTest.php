<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Models\Todo;
use Illuminate\Foundation\Testing\RefreshDatabase;

class TodoTest extends TestCase
{
    use RefreshDatabase;
    public function test_statusLabels()
    {
        $expectedLabels = [
            Todo::STATUS_NOT_STARTED => 'NOT STARTED',
            Todo::STATUS_IN_PROGRESS => 'IN PROGRESS',
            Todo::STATUS_COMPLETED => 'COMPLETED',
        ];

        $this->assertEquals($expectedLabels, Todo::statusLabels());
    }

    public function test_getStatusLabelAttribute_returns_correct_label()
    {
        $todoNotStarted = Todo::create([
            'title' => 'Test Todo',
            'details' => 'Test details',
            'status' => Todo::STATUS_NOT_STARTED,
        ]);
        $this->assertEquals('NOT STARTED', $todoNotStarted->status_label);

        $todoInProgress = Todo::create([
            'title' => 'Test Todo 2',
            'details' => 'Test details 2',
            'status' => Todo::STATUS_IN_PROGRESS,
        ]);
        $this->assertEquals('IN PROGRESS', $todoInProgress->status_label);

        $todoCompleted = Todo::create([
            'title' => 'Test Todo 3',
            'details' => 'Test details 3',
            'status' => Todo::STATUS_COMPLETED,
        ]);
        $this->assertEquals('COMPLETED', $todoCompleted->status_label);
    }

}
