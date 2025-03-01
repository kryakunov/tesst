<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Question extends Model
{
    public function block()
    {
        return $this->belongsTo(Block::class);
    }
}
