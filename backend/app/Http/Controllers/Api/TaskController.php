<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\TaskStoreRequest;
use App\Http\Requests\TaskUpdateRequest;
use App\Http\Resources\TaskResource;
use App\Models\Task;

class TaskController extends Controller
{
    
    public function index(Request $request)
    {
        $tasks = Task::where('user_id', $request->user()->id)
            ->latest()
            ->get();

        return TaskResource::collection($tasks);
    }

    public function store(TaskStoreRequest $request)
    {
        $task = Task::create([
            'user_id' => $request->user()->id,
            ...$request->validated(),
        ]);

        return new TaskResource($task);
    }

    public function show(Request $request, Task $task)
    {
        abort_if($task->user_id !== $request->user()->id, 403);
        return new TaskResource($task);
    }

    public function update(TaskUpdateRequest $request, Task $task)
    {
        abort_if($task->user_id !== $request->user()->id, 403);
        $task->update($request->validated());
        return new TaskResource($task);
    }

    public function destroy(Request $request, Task $task)
    {
        abort_if($task->user_id !== $request->user()->id, 403);
        $task->delete();
        return response()->json(['message' => 'Deleted']);
    }
}
