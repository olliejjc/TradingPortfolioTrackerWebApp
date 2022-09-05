<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Trade extends Model
{
    protected $table = 'trades';

    public function existingScreenshots(){
        return $this->hasMany('App\Models\Screenshot');
    }

    public function users()
    {
        return $this->belongsTo('App\Models\User');
    }
}
