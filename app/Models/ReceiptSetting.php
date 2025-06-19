<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReceiptSetting extends Model
{
    use HasFactory;

    // リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // このモデルで一括代入（mass assignment）してよい属性の一覧
    protected $fillable = [
        'user_id', 'postal_code', 'address_line1', 'address_line2', 'issuer_name', 'issuer_number', 'tel_fixed', 'tel_mobile', 'responsible_name'
    ];
}
