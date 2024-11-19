<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;


class Borrow extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'borrows';

    protected $fillable = [
        'uuid',
        'user_id',
        'book_id',
        'borrow_date',
        'due_date',
        'due_date_text',
        'returned',
        'return_date',
        'total_penalty',
        'penalty_paid'
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

        static::saving(function ($borrow) {
            if ($borrow->due_date) {
                $borrow->due_date_text = Carbon::parse($borrow->due_date)->format('Y-m-d');
            } else {
                $borrow->due_date_text = null;
            }
        });
    }

    /**
     * user
     *
     * @return void
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * book
     *
     * @return void
     */
    public function book()
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * search
     *
     * @param  mixed $query
     * @return void
     */
    public static function search($query)
    {
        return self::with(['user', 'book'])
        ->whereRaw("to_tsvector('english', due_date_text || ' ' || (select title from books where id = borrows.book_id) || ' ' || (select name from users where id = borrows.user_id)) @@ plainto_tsquery('english', ?)", [$query])
        ->get();
    }

}
