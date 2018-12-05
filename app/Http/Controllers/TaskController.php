<?php

namespace App\Http\Controllers;

use App\Http\Controllers\ApiController;
use App\Http\Controllers\Responses\Responder;
use App\Http\Requests\StoreTaskRequest;
use App\Http\Requests\ToggleTaskRequest;
use App\Http\Requests\UpdateTaskRequest;
use App\Task;
use App\Transformers\TaskTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TaskController extends Controller
{
    //

    private $responder;

    public function __construct(Responder $responder)
    {
        $this->middleware('auth:api');
        $this->responder=$responder;

    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $tasks=new Task();

        if ($tasks->user_id !=$request->user()->id)
        {
             $tasks->where('user_id', $request->user()->id)->where('is_private', 0)->get();
        }

        $tasks->where('user_id',$request->user()->id)->get();

        $transformed=\Fractal::item($tasks,new TaskTransformer())->toArray();

        return $this->responder->setStatus(200)->respond($transformed);
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  StoreTaskRequest  $request
     * @return \Illuminate\Http\Response
     */
    public function store(StoreTaskRequest $request)
    {
           $task=\Auth::user()->tasks()->create($request->all());

           $transformed=\Fractal::item($task,new TaskTransformer())->toArray();

           return $this->responder->setStatus(201)->respond($transformed);

    }

    public function update(UpdateTaskRequest $request, Task $task)
    {

        if ($task->user_id != $request->user()->id) {
            return $this->responder->setStatus(401)->respondWithAuthenticationError();
        }

        $task->update($request->all());

        $transformed=\Fractal::item($task,new TaskTransformer())->toArray();

        return $this->responder->setStatus(200)->respond($transformed);


        }


    public function toggle(ToggleTaskRequest $request, Task $task)
    {
        if ($task->user_id != $request->user()->id) {
            return $this->responder->setStatus(401)->respondWithAuthenticationError();
        }

        $task->update($request->all());

        $transformed=\Fractal::item($task,new TaskTransformer())->toArray();

        return $this->responder->setStatus(200)->respond($transformed);

    }


    public function destroy(Task $task)
    {

        if ($task->user_id != \Auth::user()->id)
        {
            return $this->responder->setStatus(401)->respondWithAuthenticationError();

        }

        $task->delete();

        return $this->responder->setStatus(200)->respond($task->toArray());

    }
}
