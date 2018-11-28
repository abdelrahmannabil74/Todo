<?php

namespace App\Http\Controllers;

use App\Events\Reminder;
use App\Events\TaskWatched;
use App\Http\Controllers\ApiController;
use App\Http\Requests\UpdateProfileRequest;
use App\Task;
use App\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class UserController extends ApiController
{
    //

    public function __construct()
    {
//        $this->middleware('auth:api');
    }

    public function profile(User $user)
    {
        if ($user) {
            $user->first();

            return $this->respondOK($user);
        }
         else
           return $this->respondNotFound();
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
        $task=$task->orderBy('created_at','desc')->with('user')->get()->toArray();

        return $this->respondOK($task);
    }

    public function search(Request $request)
    {

        $data=$request->input('user');

        if($data)
        {
            $user=User::with('tasks')->where('name',  $request->input('user') )->first();

            if (!$user)
            {
                return $this->respondNotFound();
            }
            return $this->respondOK($user);

        }

    }

    public function updateProfile(UpdateProfileRequest $request,User $user)
    {
          $input=$request->only('info','avatar');

        $validator = $request->validated();

        if (!$validator)
        {
            return $this->respondNotAcceptable();
        }

        else
        {
            $user->update($input);

           return $this->respondOK($user->toArray());
        }

    }

    public function watch(Request $request,Task $task)
    {
        if(!$task->is_private) {
            $task->watching()->sync($request->user()->id);
            event(new TaskWatched($request->user()->id,$task));
        }
        return $this->respondCreated($task->toArray());
    }


    public function invite(Request $request,User $user,Task $task)
    {
        if($task->user_id=$request->user()->id)
            $task->invitations()->sync($user->id);
        return $this->respondCreated($task->toArray());
    }

    public function reminder(){
        $tasks=Task::with('user')->whereDate('deadline',Carbon::tomorrow());
        foreach ($tasks as $task){
            event(new Reminder($task,$task->user));
        }
        return $this->respondCreated();
    }

    public function accept(Request $request,Task $task){
        $task->invitations()->updateExistingPivot($request->user(), array('flag' => 1), false);
        return $this->respondCreated();
    }

    public function reject(Request $request,Task $task){
        $task->invitations()->updateExistingPivot($request->user(), array('flag' => 2), false);
        return $this->respondCreated();
    }




}
