<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ingredients extends Model
{
    protected $guarded = [];
    protected $table = 'ingredients';
    protected $fillable = [
        'ingredients_name', 'ingredients_remaining','ingredients_status'
    ];
    
}
