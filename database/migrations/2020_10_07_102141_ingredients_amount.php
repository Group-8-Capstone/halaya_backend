<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class IngredientsAmount extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ingredients_amount', function (Blueprint $table) {
            $table->bigIncrements('id');
            // $table->bigInteger('ingredients_id');
            $table->string('ingredients_name');
            $table->float('ingredients_need_amount');
            $table->string('ingredients_category');
            // $table->string('status');
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
        //
    }
}
