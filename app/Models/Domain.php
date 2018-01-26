<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Domain extends Model
{
    use SoftDeletes;
    protected $dates = ['deleted_at'];
    protected $hidden = ['deleted_at'];

    public function accounts() {
        return $this->hasMany('App\Models\Account', 'domain_id');
    }

    public function language() {
        return $this->belongsTo('App\Models\Language', 'language_id');
    }

    public function fontWord() {
        return $this->belongsTo('App\Models\FontWord', 'font_word_id');
    }

    public function type() {
        return $this->belongsTo('App\Models\Type', 'type_id');
    }

    public function member() {
        return $this->belongsTo('App\Models\Member', 'member_id');
    }
}
