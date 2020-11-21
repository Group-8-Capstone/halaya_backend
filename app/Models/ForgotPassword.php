<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ForgotPassword extends Model
{
    use HasFactory;
    protected $table = 'forgotPassword';
    protected $fillable = [
        'account_id', 'phone', 'code', 'is_Valid'
    ];
}
