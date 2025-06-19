<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Receipt extends Model
{
    use HasFactory;

    // リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function bentoDetails()
    {
        return $this->hasMany(ReceiptBentoDetail::class);
    }
}
