<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IngredientsAmount extends Model
{
    protected $guarded = [];
    protected $table = 'ingredients_amount';
    protected $fillable = [
        'ingredients_id', 'ingredients_amount'
    ]; 
}
