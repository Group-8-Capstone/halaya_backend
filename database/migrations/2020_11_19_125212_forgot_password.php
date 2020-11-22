<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ForgotPassword extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        //
        Schema::create('forgotPassword', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('account_id');
            $table->string('code')->unique();
            $table->string('phone');
            $table->boolean("is_Valid");
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
        Schema::dropIfExists('forgotPassword');
    }
}
