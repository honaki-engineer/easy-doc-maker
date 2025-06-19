<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BentoName extends Model
{
    use HasFactory;

    // リレーション
    public function bentoBrand()
    {
        return $this->belongsTo(BentoBrand::class);
    }
}
