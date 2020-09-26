<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Stock extends Model
{
    protected $guarded = [];
    protected $table = 'stocks';
    protected $fillable = [
        'ube_kilo', 'delivery_date','expected_output','stock_status'
    ];
    
}
