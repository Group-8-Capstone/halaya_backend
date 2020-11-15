<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class IngredientsAmount extends Model
{
    protected $guarded = [];
    protected $table = 'ingredients_amount';
    use SoftDeletes;
    protected $fillable = [
        'ingredients_name', 'ingredients_need_amount','ingredients_unit', 'ingredients_category'
    ]; 
}
