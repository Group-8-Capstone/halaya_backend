<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeleveredOrder extends Model
{
    // use HasFactory;
    protected $guarded = [];
    protected $table = 'delivered_order';
    protected $fillable = [
        'customer_name', 
        'delivery_address',
        'halaya_qty',
        'ubechi_qty', 
        'delivery_date',
        'order_status',
        'distance'
    ];
}
