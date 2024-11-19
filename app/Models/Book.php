<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Book extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'books';

    protected $fillable = [
        'uuid',
        'title',
        'author',
        'isbn',
        'status',
        'publication_date',
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

        static::deleting(function ($book) {
            $book->borrow()->each(function ($borrow) {
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
     * @param  mixed $status
     * @return void
     */
    public static function search($query, $status = null)
    {
        return self::whereRaw(
            "to_tsvector('english', title || ' ' || author || ' ' || isbn || ' ' || COALESCE(status::text, '')) @@ plainto_tsquery('english', ?)",
            [$query]
        )->get();
    }

}
