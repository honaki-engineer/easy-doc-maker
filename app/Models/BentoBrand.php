<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BentoBrand extends Model
{
    use HasFactory;

    // リレーション
    public function bentoNames()
    {
        return $this->hasMany(BentoName::class);
    }
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // このモデルで一括代入（mass assignment）してよい属性の一覧
    protected $fillable = [
        'name', 'user_id'
    ];
}
