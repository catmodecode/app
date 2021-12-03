<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Contracts\Auth\Access\Authorizable as AuthorizableContract;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Laravel\Lumen\Auth\Authorizable;

/**
 * User class
 * @property int $id
 * @property string $email
 * @property string $name
 * @property string $phone
 * @property Carbon $created_at
 * @property Carbon $updated_at
 * @property Group $group
 * @property Collection $tokens
 * @property string $country
 * @property string $city
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
        'name', 'email', 'password', 'phone',
    ];

    /**
     * The attributes excluded from the model's JSON form.
     *
     * @var array
     */
    protected $hidden = [
        'password',
    ];

    public function groups()
    {
        return $this->belongsToMany(Group::class, 'user_group');
    }

    public function tokens()
    {
        return $this->hasMany(Token::class, 'tokens');
    }

    public function setPasswordAttribute($plainPassword)
    {
        $this->attributes['password'] = User::getHashPassword($plainPassword);
    }

    protected static function getHashPassword(string $plainPassword): string
    {
        return app('hash')->make($plainPassword);
    }
}
