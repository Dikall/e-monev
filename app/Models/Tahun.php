<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tahun extends Model
{
    use HasFactory;

    protected $table = 'tahuns';
    
    protected $fillable = ['tahun'];

    public function kategoris()
    {
        return $this->hasMany(Kategori::class, 'tahun_id');
    }
}