<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Block extends Model
{
    public function questions()
    {
        return $this->hasMany(Question::class);
    }

    public function anketa()
    {
        return $this->belongsTo(Anketa::class);
    }
}
