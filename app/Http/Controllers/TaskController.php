<?php

namespace App\Http\Controllers;

use App\Http\Resources\TaskCollection;
use App\Http\Resources\TaskResource;
use App\Models\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new TaskCollection(Task::orderBy('order')->get());
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'description' => 'required|max:500',
        ]);

        $data['is_completed'] = 0;
        $data['order'] = Task::max("order") + 1;

        return new TaskResource(Task::create($data)); // 201
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function show(Task $task)
    {
        return new TaskResource($task);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, Task $task)
    {
        $data = $request->validate([
            'description' => 'required|max:500',
            'is_completed' => 'required|boolean'
        ]);

        $task->update($data);

        return new TaskResource($task);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Task  $task
     * @return \Illuminate\Http\Response
     */
    public function destroy(Task $task)
    {
        $task = DB::transaction(function() use ($task) {
            $task->delete();

            $order = $task->order;

            DB::statement("
                UPDATE tasks t
                JOIN (
                    SELECT
                        ROW_NUMBER() OVER(ORDER BY `order`) - 1 + $order r,
                        id
                    FROM tasks
                    WHERE `order` >= $order
                    GROUP BY id
                    ORDER BY `order`
                ) o ON o.id = t.id
                SET t.`order` = o.r
                WHERE o.id = t.id
            ");

            return $task;
        });

        return new TaskResource($task);
    }

    public function sort(Request $request, Task $task)
    {
        $data = $request->validate([
            'old' => 'required|integer',
            'new' => 'required|integer'
        ]);

        $old = +$data['old'] + 1;
        $new = +$data['new'] + 1;

        $task = DB::transaction(function () use ($old, $new, $task) {

            $id = $task->id;

            if($old < $new) { // pababa sa list

                $to = $old;
                $from = $new;
                $increment = $old - 1;

            } else { // pasaka sa list

                $to = $new;
                $from = $old;
                $increment = $new;
            }

            DB::statement("
                UPDATE tasks t
                JOIN (
                    SELECT
                        ROW_NUMBER() OVER(ORDER BY `order`) + $increment r,
                        id
                    FROM tasks
                    WHERE `order` BETWEEN $to AND $from AND id <> $id
                    GROUP BY id
                    ORDER BY `order`
                ) o ON o.id = t.id
                SET t.`order` = o.r
                WHERE o.id = t.id
            ");

            $task->order = $new;
            $task->save();

            return $task;

        });

        return new TaskResource($task);
    }
}
