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
        Schema::create('comments', function (Blueprint $table) {
            $table->id();
            $table->string("type");
            $table->foreignId('user_id')->default(0);
            $table->string("fullname");
            $table->string("email");
            $table->string("title" , 1000);
            $table->foreignId('post_type_id')->constrained("post_types")->onDelete('cascade');
            $table->text("content");
            $table->unsignedBigInteger("origin_parent_id")->default(0);
            $table->unsignedBigInteger("parent_id")->default(0);
            $table->tinyInteger("depth" , false , true);
            $table->string("ip");
            $table->string("status")->default("pending");
            $table->tinyInteger('rating' , false , true)->nullable();
            $table->text("extra")->nullable();
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
        Schema::dropIfExists('comments');
    }
};
