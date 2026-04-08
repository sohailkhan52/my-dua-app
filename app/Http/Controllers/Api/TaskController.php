<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    /**
     * Get authenticated user
     */
    private function user()
    {
        $user = auth('api')->user();
        return $user;
    }

    /**
     * Display single task
     */
    public function show($id)
    {

        $task = Task::find($id);

        if (! $task) {
            return response()->json([
                'status'  => false,
                'message' => 'Task not found',
            ], 404);
        }

        // Unauthorized access (belongs to another user)
        if ($task->user_id !== $this->user()->id) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized access',
            ], 403);
        }

        return response()->json([
            'status'  => true,
            "message" => "Task Retrieved Successfully",
            "data"    => $task,
        ]);
    }
    /**
     * Display all tasks of logged-in user
     */
    public function index()
    {

        $tasks = $this->user()->tasks()->latest()->get();
        return response()->json([
            'status'  => true,
            "message" => "Tasks Retrieved Successfully",
            "data"    => $tasks,
        ]);
    }
    /**
     * Store a new task
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "title"    => "required|string|max:255",
            "status"   => "in:pending,completed|max:1000",
            "due_date" => "nullable|date|after_or_equal:today",
        ]);

        //return validation errors
        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                "message" => "Validation Error",
                "error"   => $validator->errors(),
            ], 422);
        }
        //create task
        $task = $this->user()->tasks()->create([
            'title'       => $request->title,
            'description' => $request->description,
            'status'      => $request->status ?? "pending",
            'due_date'    => $request->due_date,
        ]);

        return response()->json([
            "status"  => true,
            "message" => "Task created Successfully",
            "data"    => $task,
        ], 201);

    }
    /**
     * Update existing task
     */
    public function update(Request $request, $id)
    {
        $validator = Validator::make(
            array_merge($request->all(), ['id' => $id]),
            [
                'id'          => 'required|exists:tasks,id',
                'title'       => 'sometimes|required|string|max:255',
                'status'      => 'sometimes|in:pending,completed',
                'description' => 'nullable|string|max:1000',
                'due_date'    => 'nullable|date|after_or_equal:today',
            ]
        );

        if ($validator->fails()) {
            return response()->json([
                'status' => false,
                "Mesage" => "Validation errors",
                "data"   => $validator->errors(),
            ], 422);
        }

        $task = Task::find($id);

        // Check ownership + existence
        if (! $task) {
            return response()->json([
                "status"  => false,
                "message" => "Task not found",
                "data"    => null,
            ], 404);
        }
        if ($task->user_id !== $this->user()->id) {
            return response()->json([
                'status'  => false,
                'message' => 'Unauthorized',
            ], 403);
        }

        $task->update($request->only([
            "title", "description", "status", "due_date",
        ]));
        return response()->json([
            "status"  => true,
            "message" => "Task updated successfully",
            "data"    => $task,
        ]);
    }
    /**
     * Delete a task
     */
    public function destroy($id)
    {
        $task = $this->user()->tasks()->find($id);
        if (! $task) {
            return response()->json([
                "status"  => false,
                "message" => "Task not found",
            ], 404);
        }

        $result = $task->delete();
        if ($result) {
            return response()->json([
                "status"  => true,
                "message" => "Task Deleted successfully",
            ]);
        } else {
            return response()->json([
                "status"  => false,
                "message" => "the task did not deleted plz try again",
            ]);
        }
    }

}
