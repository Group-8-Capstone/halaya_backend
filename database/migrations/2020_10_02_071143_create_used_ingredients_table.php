<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsedIngredientsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('used_ingredients', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('ingredients_id');
            $table->float('used_ingredients_amount');
            $table->string('ingredients_unit');
            $table->string('ingredients_name');
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
        Schema::dropIfExists('used_ingredients');
    }
}
