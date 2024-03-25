<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Taxonomy extends Model
{
    use HasFactory, Features;

    public function post_types()
    {
        return $this->belongsToMany(PostType::class, "post_types_taxonomies");
    }
}
