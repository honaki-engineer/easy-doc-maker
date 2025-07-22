<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CustomerName extends Model
{
    use HasFactory;

    protected $fillable = [
        'name', 'user_id'
    ];

    // リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function receipts()
    {
        return $this->hasMany(Receipt::class);
    }
}
