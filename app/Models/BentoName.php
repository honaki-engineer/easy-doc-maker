<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BentoName extends Model
{
    use HasFactory;

    // リレーション
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function bentoBrand()
    {
        return $this->belongsTo(BentoBrand::class);
    }

    // このモデルで一括代入（mass assignment）してよい属性の一覧
    protected $fillable = [
        'name', 'user_id', 'bento_brand_id'
    ];
}
