<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Anketa extends Model
{
    public function blocks()
    {
        return $this->hasMany(Block::class);
    }
}
