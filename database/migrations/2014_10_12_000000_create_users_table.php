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
        Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('username')->unique();
            $table->string('email')->unique();
            $table->string('phone')->unique();
            $table->string('fullname');
            $table->string('password');
            $table->string('role');
            $table->rememberToken();
            $table->text('description')->nullable();
            $table->string('theme_color')->default(getDefaultThemeUser("theme_color"));
            $table->string('theme_color_hover')->default(getDefaultThemeUser("theme_color_hover"));
            $table->boolean('email_verified')->default(0);
            $table->boolean('phone_verified')->default(0);
            $table->string('status' , 100)->default("deactive");
            $table->timestamp('email_verified_at')->nullable();
            $table->timestamp('phone_verified_at')->nullable();
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
        Schema::dropIfExists('users');
    }
};
