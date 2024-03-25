<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
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
        Schema::create('forms_schemas', function (Blueprint $table) {
            $table->id();
            $table->string("title");
            $table->string("type");
            $table->boolean("is_login_required")->default(false);
            $table->boolean("is_captcha_required")->default(false);
            $table->text("schema");
            $table->string("status")->default("pending");
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
        DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        Schema::dropIfExists('forms_schemas');
        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
};
