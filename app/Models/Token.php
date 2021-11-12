<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Token class
 *
 * @property string $token
 * @property string $delete_at
 * @property User $user
 */
class Token extends Model
{
    use HasFactory;

    protected $primaryKey = 'token';
    public $incrementing = false;
    public $timestamps = false;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'token', 'delete_at', 'user_id'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
