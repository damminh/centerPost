<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Requirement extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];

    public function members() {
        return $this->belongsToMany('App\Models\Member', 'requirement_member', 'requirement_id', 'member_id');
    }

    public function posts() {
        return $this->hasMany('App\Models\Post', 'requirement_id');
    }

    public function domains() {
        return $this->belongsToMany('App\Models\Domain', 'requirement_domain', 'requirement_id', 'domain_id');
    }
}
