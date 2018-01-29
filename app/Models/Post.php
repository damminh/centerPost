<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Post extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];

    public function member() {
        return $this->belongsTo('App\Models\Member', 'member_id');
    }

    public function requirement() {
        return $this->belongsTo('App\Models\Requirement', 'requirement_id');
    }

    public function histories() {
        return $this->hasMany('App\Models\HistoryPost', 'post_id');
    }
}
