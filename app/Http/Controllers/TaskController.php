<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\ToggleTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Task;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends ApiController
{
    //


    public function __construct()
    {
        $this->middleware('auth:api');

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tasks=new Task();

        if ($tasks->user_id !=$request->user()->id) {
             Task::where('user_id', $request->user()->id)->where('is_private', 0)->get();
        }

        $tasks->where('user_id',$request->user()->id)->get();

        return $this->respondOK($tasks->toArray()) ;
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {
        $input = $request->all();

        if(!$input){
            return $this->respondNotAcceptable();
        }

        $input['user_id']=$request->user()->id;

        $task = Task::create($input);

        return $this->respondCreated($task->toArray());

    }

    public function update(UpdateTaskRequest $request, Task $task)
    {
            $input = $request->all();

        if ($task->user_id != $request->user()->id) {
            return $this->respondNotAuthenticated();
        }

        if (!$input) {
            return $this->respondNotAcceptable();
        }


        $task->where('id', $task->id)->update($input);


        return $this->respondOK($task->toArray());


        }


    public function toggle(ToggleTaskRequest $request, Task $task)
    {

        $input = $request->only('is_complete');

        if ($task->user_id != $request->user()->id) {
            return $this->respondNotAuthenticated();
        }

        if(!$input){
            return $this->respondNotAcceptable();
        }

        $task->update($input);

        return $this->respondOK($task->toArray());
    }


    public function destroy(Task $task)
    {

        if ($task->user_id != \Auth::user()->id) {
            return $this->respondNotAuthenticated();
        }

        $task->delete();

         return $this->respondOK($task->toArray());

    }
}
