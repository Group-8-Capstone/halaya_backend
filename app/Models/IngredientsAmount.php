<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientsAmount extends Model
{
    protected $guarded = [];
    protected $table = 'ingredients_amount';
    protected $fillable = [
<<<<<<< HEAD
        'ingredients_name', 'ingredients_need_amount','ingredients_category'
=======
        'ingredients_name', 'ingredients_need_amount', 'ingredients_category'
>>>>>>> f823eacfb687abf10fdc89778e13ab0f6258f054
    ]; 
}
