<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentMethod extends Model
{
    use HasFactory;

    // このモデルで一括代入（mass assignment）してよい属性の一覧
    protected $fillable = [
        'name', 'user_id'
    ];

    // リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
