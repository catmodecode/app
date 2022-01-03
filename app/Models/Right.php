<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 */
class Right extends Model
{
    use HasFactory;

    public function user()
    {
        $this->morphedByMany(User::class, 'rightable');
    }

    public function group()
    {
        $this->morphedByMany(Group::class, 'rightable');
    }
}
