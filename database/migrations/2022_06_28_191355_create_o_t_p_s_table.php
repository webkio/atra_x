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
        Schema::create('o_t_p_s', function (Blueprint $table) {
            $table->id();
            $table->string("type"); // [verify_email , verify_phone , reset_password]
            $table->string("via"); // [sms , email , notification]
            $table->foreignId("user_id");
            $table->string("user_hash_id");
            $table->string("client_id");
            $table->string("client_code");
            $table->string("data")->nullable();
            $table->tinyInteger("attempt" , false , true)->default(0);
            $table->boolean("expired")->default(false);
            $table->boolean("seen")->default(false);
            $table->timestamp("created_at")->nullable();
            $table->timestamp("expired_at")->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('o_t_p_s');
    }
};
