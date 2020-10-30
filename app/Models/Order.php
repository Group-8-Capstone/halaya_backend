<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];
    protected $table = 'orders';
    protected $fillable = [
        'customer_name', 
        'customer_address',
        'contact_number', 
        'order_quantity', 
        'delivery_date',
        'order_status',
        'longitude',
        'latitude',
        'distance'
    ];
}


