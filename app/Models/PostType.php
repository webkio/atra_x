<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostType extends Model
{
    use HasFactory , Features;

    public function taxonomies()
    {
        return $this->belongsToMany(Taxonomy::class, "post_types_taxonomies")->withTimestamps();
    }

    public function comments()
    {
        return $this->hasMany(Comment::class)->where('type', 'comment');
    }

    public function comments_rating()
    {
        return $this->hasMany(Comment::class)->where("type", "rating");
    }

    public function views()
    {
        return $this->hasMany(View::class);
    }

    public function meta()
    {
        return $this->hasMany(PostTypeMeta::class);
    }

    public function history_action()
    {
        $query = $this->hasMany(HistoryAction::class , "model_id")->where("model_type" , get_class($this));
        
        return $query;
    }
}
