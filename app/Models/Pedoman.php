<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Pedoman extends Model
{
    use HasFactory;

    protected $table = 'pedomen';

    protected $fillable = [
        'file_name',
        'file_data',
    ];
}
