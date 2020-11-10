<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateRecordedProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('recorded_products', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->string('product_name');
            $table->integer('remaining_quantity')->nullable();
            $table->integer('total_ordered')->nullable();
            $table->string('availability_status');
            $table->softDeletes();
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
        Schema::dropIfExists('recorded_products');
    }
}
