<?php

namespace App\Transformers;

use App\User;
use League\Fractal\TransformerAbstract;

class UserTransformer extends TransformerAbstract
{
    //    public $defaultIncludes=['id','title','deadline','is_private','is_complete'];

    protected $availableIncludes=['tasks'];
    /**
     * A Fractal transformer.
     *
     * @return array
     */
    public function transform(User $user)
    {
        return [
            //
            'id'=>$user->id,
            'name'=>$user->name,
            'email'=>$user->email,
            'info'=>$user->info,
            'avatar'=>$user->avatar,

        ];
    }

    public function includeTasks(User $user)
    {
        return $this->collection($user->tasks(),new TaskTransformer());
    }
}
