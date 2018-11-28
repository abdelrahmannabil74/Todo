<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Task extends Model
{
    //

    protected $fillable = [
        'title', 'is_private', 'is_complete','deadline','user_id',


    ];


    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function watching(){
        return $this->belongsToMany('App\User','watching');
    }

    public function invitations(){
        return $this->belongsToMany('App\User','invitations')->withPivot('flag');
    }

}
