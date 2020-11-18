<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryRangeQty extends Model
{
    protected $guarded = [];
    protected $table = 'delivery_range_qty';
    protected $fillable = [
        'ubehalayatub',
        'ubehalayajar'
    ];
}
