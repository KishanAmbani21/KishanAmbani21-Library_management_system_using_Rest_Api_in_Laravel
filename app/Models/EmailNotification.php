<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use MongoDB\Laravel\Eloquent\Model;

class EmailNotification extends Model
{
    use HasFactory;

    protected $connection = 'mongodb';

    // protected $collection = 'notifications';

    protected $fillable = [
        'user_id',
        'book_id',
        'due_date',
        'sent_at',
        'penalty'
    ];
}
