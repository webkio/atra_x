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
        Schema::create('taxonomies', function (Blueprint $table) {

            $table->id();
            $table->string("type");
            $table->string("title")->unique();
            $table->string("slug");
            $table->longtext("body")->nullable();
            $table->longtext("body_raw")->nullable();
            $table->string("thumbnail_url")->nullable();
            $table->string("status");
            $table->longText("extra")->nullable();
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
        Schema::dropIfExists('taxonomies');
    }
};
