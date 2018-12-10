<?php

namespace App\Transformers;

use App\Task;
use League\Fractal\TransformerAbstract;

class TaskTransformer extends TransformerAbstract
{
//    public $defaultIncludes=['id','title','deadline','is_private','is_complete'];
    public $defaultIncludes=['user'];
    protected $availableIncludes=['users'];

    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(Task $task)
    {
        return [
            'id' =>$task->id ,
            'title'  =>$task->title,
            'deadline' =>$task->deadline ,
            'is_private' =>(boolean) $task->is_private ,
            'is_complete' =>(boolean) $task->is_complete

        ];
    }

//    public function includeId(Task $task){
//        return $this->primitive($task->id);
//    }
//    public function includeTitle(Task $task){
//        return $this->primitive($task->title);
//    }
//    public function includeDeadline(Task $task){
//        return $this->primitive($task->deadline);
//    }
//    public function includeIsPrivate(Task $task){
//        return $this->primitive((boolean)$task->is_private);
//    }
//    public function includeIsComplete(Task $task){
//        return $this->primitive((boolean)$task->is_complete);
//    }
    public function includeUser(Task $task)
    {
        return $this->item($task->user,new UserTransformer());
    }
}
