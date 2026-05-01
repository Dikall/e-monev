<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kuemonev extends Model
{
    use HasFactory;

    protected $table = 'kuemonevs';

    protected $fillable = ['file_name', 'file_data'];
}
