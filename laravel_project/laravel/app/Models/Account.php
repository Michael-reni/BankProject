<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Account extends Model
{
    use HasFactory;

    protected $primaryKey = 'account_number';
    protected $keyType = 'string';
    public $incrementing = false;
    protected $casts = [
        'id' => 'string'
    ];
}
