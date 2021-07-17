<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Auth\Authenticatable;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Contracts\Auth\Authenticatable as AuthenticatableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Laravel\Lumen\Auth\Authorizable;

/**
 * User class
 * @property int $id
 * @property string $email
 * @property string $name
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @method string getHashPassword()
 */
class User extends Model implements AuthorizableContract
{
    use Authorizable;
    use HasFactory;
    use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password'
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function setPasswordAttribute($plainPassword)
    {
        $this->attributes['password'] = User::getHashPassword($plainPassword);
    }

    protected static function getHashPassword(string $plainPassword): string
    {
        return app('hash')->make($plainPassword);
    }
}
