<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $guarded = [];

    protected $fillable = [
        'customersName', 'address','contactNumber', 'orderQuantity', 'deliveryDate','orderStatus'
    ];
    

}


