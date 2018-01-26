<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HistoryPost extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];

    public function member() {
        return $this->belongsTo('App\Models\Member', 'member_id');
    }

    public function post() {
        return $this->belongsTo('App\Models\Post', 'post_id');
    }
}
