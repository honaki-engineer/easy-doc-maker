<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptBentoDetail extends Model
{
    use HasFactory;

    // リレーション
    public function receipt()
    {
        return $this->belongsTo(Receipt::class);
    }
}
