<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderedProduct extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'ordered_product';
    protected $fillable = [
     'make_product_id', 'ordered_product_quantity'
    ];
}
