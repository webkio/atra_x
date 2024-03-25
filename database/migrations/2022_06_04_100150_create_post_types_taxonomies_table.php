<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('post_types_taxonomies', function (Blueprint $table) {
           $table->primary(["post_type_id" , "taxonomy_id"]); // block duplicate row 
           $table->foreignId('post_type_id')->constrained("post_types")->onDelete('cascade');
           $table->foreignId('taxonomy_id')->constrained("taxonomies")->onDelete('cascade');
           $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('post_types_taxonomies');
    }
};
