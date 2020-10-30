<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MakeProduct extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'make_product';
    use SoftDeletes;
    protected $fillable = [
        'product_id', 'product_remaining','product_status'
    ]; 
}
