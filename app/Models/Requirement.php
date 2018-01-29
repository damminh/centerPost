<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requirement extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];

    public function member() {
        return $this->belongsTo('App\Models\Member', 'member_id');
    }

    public function posts() {
        return $this->hasMany('App\Models\Post', 'requirement_id');
    }
}
