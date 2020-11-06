<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateOrderTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->integer('customer_id');
            $table->string('receiver_name');
            $table->bigInteger('contact_number');
            $table->string('customer_address');
            $table->smallInteger('ubeHalayaJar_qty');
            $table->smallInteger('ubeHalayaTub_qty');
            $table->date('preferred_delivery_date');
            $table->double('distance');
            $table->string('order_status');
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
        Schema::dropIfExists('orders');
    }
}
