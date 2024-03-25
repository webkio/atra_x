<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HistoryAction extends Model
{
    use HasFactory;
    const UPDATED_AT = null;

    public function relatedModels()
    {
        return $this->morphTo("model");
    }

    public function user()
    {
        return $this->belongsTo(User::class , "by");
    }

}
