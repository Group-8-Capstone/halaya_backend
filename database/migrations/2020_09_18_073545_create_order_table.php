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
            // $table->string('customer_address');
            $table->string('building_or_street');
            $table->string('barangay');
            $table->string('city_or_municipality');
            $table->string('province');
            $table->smallInteger('ubehalayajar_qty');
            $table->smallInteger('ubehalayatub_qty');
            $table->double('total_payment');
            $table->date('preferred_delivery_date');
            $table->double('distance');
            $table->string('order_status');
            $table->string('mark_status');
            $table->string('mark_adminstatus');
            $table->double('latitude');
            $table->double('longitude');
            $table->integer('postcode');
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
