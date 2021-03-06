<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Member extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];

    public function domains() {
        return $this->hasMany('App\Models\Domain', 'member_id');
    }

    public function posts() {
        return $this->hasMany('App\Models\Post', 'member_id');
    }
}
