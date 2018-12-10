<?php

namespace App\Http\Controllers;

use App\Events\Reminder;
use App\Events\TaskWatched;
use App\Http\Controllers\ApiController;
use App\Http\Controllers\Responses\Responder;
use App\Http\Requests\UpdateProfileRequest;
use App\Task;
use App\Transformers\TaskTransformer;
use App\Transformers\UserTransformer;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends Controller
{
    //

    private $responder;
    public function __construct(Responder $responder)
    {

        $this->middleware('auth:api');
           $this->responder=$responder;
    }

    public function profile(User $user)
    {
        if (!$user)

            return $this->responder->setStatus(404)->rerespondWithError();

        else
        {
            $user->first();

            $transformed=\Fractal::item($user,new UserTransformer())->toArray();

            return $this->responder->setStatus(200)->respond($transformed);

        }

    }

    public function newsFeed(Request $request)
    {

        $task=Task::where('user_id',$request->user()->id);


        if($request->input('status')){
            $task=$task->where('is_complete',$request->input('status'));
        }

        if($request->input('owner')){
            $task=$task->where('user_id',$request->input('owner'));
        }

        if($request->input('date')){
            $task=$task->whereDate('created_at',$request->input('date'));
        }

        $task=$task->orderBy('created_at','desc')->with('user')->get();

//        dd($task);

        $transformed=\Fractal::includes('user')->collection($task,new TaskTransformer());

        return $this->responder->setStatus(200)->respond($transformed);

    }

    public function search(Request $request)
    {

        $data=$request->input('user');

        if($data)
        {
            $user=User::with('tasks')->where('name',  $request->input('user') )->first();

            if (!$user)

            {
                return $this->responder->setStatus(404)->rerespondWithError();

            }

            return $this->responder->setStatus(200)->respond($user);

        }

    }

    public function updateProfile(UpdateProfileRequest $request,User $user)
    {

        if (!$request->all())
        {
            return $this->responder->setStatus(406)->respondWithError();
        }

        else
        {
            $user->update($request->all());

            $transformed=\Fractal::item($user,new UserTransformer())->toArray();

           return $this->responder->setStatus(200)->respond($transformed);
        }

    }

    public function watch(Request $request,Task $task)
    {
        if(!$task->is_private)
        {
            $task->watching()->sync($request->user()->id);
            event(new TaskWatched($request->user()->id,$task));
        }

        return $this->responder->setStatus(201)->respond($task->toArray());
    }


    public function invite(Request $request,User $user,Task $task)
    {
        if($task->user_id=$request->user()->id)

        $task->invitations()->sync($user->id);

        return $this->responder->setStatus(201)->respond($task->toArray());
    }

    public function reminder()
    {
        $tasks=Task::with('user')->whereDate('deadline',Carbon::tomorrow());

        foreach ($tasks as $task)
        {
            event(new Reminder($task,$task->user));
        }

        return $this->responder->setStatus(201)->respond();
    }

    public function accept(Request $request,Task $task)
    {
        $task->invitations()->updateExistingPivot($request->user(), array('flag' => 1), false);

        return $this->responder->setStatus(201)->respond();    }

    public function reject(Request $request,Task $task)
    {
        $task->invitations()->updateExistingPivot($request->user(), array('flag' => 2), false);

        return $this->responder->setStatus(201)->respond();
    }




}
