<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Kategori extends Model
{
    use HasFactory;

    protected $table = 'kategoris';

    protected $fillable = [
        'name',
        'tahun_id',
    ];

    public function tahun()
    {
        return $this->belongsTo(Tahun::class, 'tahun_id');
    }

    public function tenggat()
    {
        return $this->hasOne(Tenggat::class);
    }

    public function publicBodies()
    {
        return $this->hasMany(PublicBody::class, 'kategori_id');
    }

}


