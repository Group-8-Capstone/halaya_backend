<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ingredientsView extends Model
{
    // use HasFactory;
    protected $guarded = [];
    protected $table = 'view_ingredients';
    protected $fillable = [
        'ingredients_name', 'ingredients_need_amount','ingredients_remaining','ingredients_status','used_ingredients_amount'
    ]; 
}
