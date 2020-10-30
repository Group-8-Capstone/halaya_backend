<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;


class Ingredients extends Model
{
    protected $guarded = [];
    protected $table = 'ingredients';
    use SoftDeletes;
    protected $fillable = [
        'ingredients_amount_id', 'ingredients_remaining','ingredients_status','ingredients_category'
    ];
    
}
