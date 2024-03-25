<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class Comment extends Model
{
    use HasFactory, Features;

    public function postType()
    {
        return $this->belongsTo(PostType::class , "post_type_id");
    }

    public function user()
    {
        return $this->belongsTo(User::class , "user_id");
    }

    public function deletePlusChilds()
    {
        $deleted = 0;
        $id = getTypeID($this);
        
        if($id){
            $deleted = DB::table($this->getTable())->where("origin_parent_id" , $id)->delete();
            $this->delete();
            $deleted++;
        }

        return $deleted;
    }
}
