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
    public function paymentMethod()
    {
        return $this->belongsTo(PaymentMethod::class);
    }
    public function bentoDetails()
    {
        return $this->hasMany(ReceiptBentoDetail::class);
    }

    protected $fillable = [
        'user_id', 
        'payment_method_id',
        'customer_name',
        'issued_at',
        'postal_code',
        'address_line1',
        'address_line2',
        'issuer_name',
        'issuer_number',
        'tel_fixed',
        'tel_mobile',
        'responsible_name',
        'receipt_note',
        'subtotal',
        'tax_total',
        'total',
        'remarks',
    ];
}
