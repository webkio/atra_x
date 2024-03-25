<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Form extends Model
{
    use HasFactory , Features;


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function formsSchema()
    {
        return $this->belongsTo(FormsSchema::class , "form_schema_id");
    }

}
