<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RecordedProduct extends Model
{
    use HasFactory;
    protected $guarded = [];
    protected $table = 'recorded_products';
    use SoftDeletes;
    protected $fillable = [
        'product_name', 'remaining_quantity','total_ordered','availability_status'
    ]; 
}
