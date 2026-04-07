<?php
namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    private function user()
    {
        $user = auth('api')->user();
        return $user;
    }

    public function show($id)
    {

        $task = $this->user()->task()->find($id);
        if (! $task) {
            return response()->json([
                'Status'  => false,
                "message" => "Task not found",
            ]);
        }
        return response()->json([
            'Status'  => true,
            "message" => "Task Retrieved Successfully",
            "data"    => $task,
        ]);
    }
    public function index()
    {

        $tasks = $this->user()->task()->latest()->get();
        if (! $tasks) {
            return response()->json([
                'Status'  => false,
                "message" => "Tasks Retrieving error occure",
            ]);
        }
        return response()->json([
            'Status'  => true,
            "message" => "Tasks Retrieved Successfully",
            "data"    => $tasks,
        ]);
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            "title"    => "required|string",
            "status"   => "in:pending,completed",
            "due_date" => "nullable|date",
        ]);

        if ($validator->fails()) {
            return response()->json([
                'status'  => false,
                "message" => "Validation Error",
                "error"   => $validator->errors(),
            ], 422);
        }

        $task = $this->user()->task()->create([
            'title'       => $request->title,
            'description' => $request->description,
            'status'      => $request->status ?? "pending",
            'due_date'    => $request->due_date,
        ]);

        if (! $task) {
            return response()->json([
                'Status'  => false,
                "message" => "task not created ",
            ], 422);
        }

        return response()->json([
            "Status"  => true,
            "message" => "Task created Successfully",
            "data"    => $task,
        ]);

    }
}
