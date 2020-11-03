<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];
    protected $table = 'orders';
    protected $fillable = [
        'customer_id',
        'customer_address',
        'contact_number', 
        'ubeHalayaJar_qty',
        'ubeHalayaTub_qty',
        'preferred_delivery_date',
        'distance',
        'order_status',
    ];
}


