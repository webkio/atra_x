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
        Schema::create('files', function (Blueprint $table) {
            $table->id();
            $table->string('original_title');
            $table->string('current_title');
            $table->string('format');
            $table->string('group_type');
            $table->decimal('size' , 10 , 5 , true);
            $table->string('url');
            $table->longText('dimension')->nullable();
            $table->foreignId('user_id');
            $table->string('user_fullname');
            $table->string('source');
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
        Schema::dropIfExists('files');
    }
};
