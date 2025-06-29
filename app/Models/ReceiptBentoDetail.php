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

    protected $fillable = [
        'receipt_id',
        'bento_brand_name',
        'bento_name',
        'bento_fee',
        'tax_rate',
        'bento_quantity',
        'unit_price',
        'amount',
    ];
}
