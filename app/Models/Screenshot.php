<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Screenshot extends Model
{
    protected $table = 'screenshots';

    public function trades()
    {
        return $this->belongsTo('App\Trade');
    }
}
