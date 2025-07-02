<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transaction extends Model
{
    //
    protected $fillable = [
        'user_id',
        'amount',
        'transaction_id',
        'status',
        'date',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
