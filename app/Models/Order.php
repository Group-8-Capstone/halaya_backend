<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class Order extends Model
{
    protected $guarded = [];
    protected $table = 'orders';
    protected $fillable = [
        'customer_id',
        'receiver_name',
        // 'customer_address',
        'building/street',
        'barangay',
        'city/municipality',
        'province',
        'contact_number', 
        'ubehalayajar_qty',
        'ubehalayatub_qty',
        'total_payment',
        'preferred_delivery_date',
        'distance',
        'latitude',
        'longitude',
        'postcode',
        'order_status',
        'mark_status',
        'mark_adminstatus',
    ];

    protected $casts = [
        'preferred_delivery_date' => 'date'
    ];

    // public function getPreferredDeliveryDateAttribute($date)
    // {
    //     return Carbon::parse($date);
    // }
}


