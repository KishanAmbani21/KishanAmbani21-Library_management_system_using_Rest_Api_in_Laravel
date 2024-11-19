<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;
use Illuminate\Support\Str;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, SoftDeletes;

    protected $table = "users";

    protected $fillable = [
        'uuid',
        'name',
        'email',
        'password',
        'roles'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * booted
     *
     * @return void
     */
    protected static function booted()
    {
        parent::boot();

        static::creating(function ($user) {
            $user->uuid = (string) Str::uuid();
        });

        static::deleting(function ($user) {
            $user->borrow()->each(function ($borrow) {
                $borrow->delete();
            });
        });
    }

    /**
     * borrow
     *
     * @return void
     */
    public function borrow()
    {
        return $this->hasMany(Borrow::class);
    }

    /**
     * search
     *
     * @param  mixed $query
     * @return void
     */
    public static function search($query)
    {
        $formattedQuery = str_replace(' ', ' & ', $query);

        return self::whereRaw("to_tsvector('english', name || ' ' || email || ' ' || roles) @@ to_tsquery(?)", [$formattedQuery]);
    }
}
