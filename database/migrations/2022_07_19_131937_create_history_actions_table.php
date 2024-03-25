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
        Schema::create('history_actions', function (Blueprint $table) {
            $table->id();
            $table->morphs("model");
            $table->text("archive_raw_before")->nullable();
            $table->text("archive_raw_after")->nullable();
            $table->string("changes" , 1024)->nullable();
            $table->string("description" , 1024);
            $table->string("action" , 512);
            $table->foreignId("by");
            $table->string("by_raw" , 512);
            $table->timestamp("created_at");
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('history_actions');
    }
};
