<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostTypeMeta extends Model
{
    use HasFactory;

    public function postType()
    {
        return $this->belongsTo(PostType::class , "post_type_id");
    }
}
