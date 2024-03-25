<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class View extends Model
{
    use HasFactory;

    public function postType()
    {
        return $this->belongsTo(PostType::class , "post_type_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class , "user_id");
    }
}
