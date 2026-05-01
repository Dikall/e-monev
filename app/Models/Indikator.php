<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Indikator extends Model
{
    use HasFactory;

    protected $fillable = [
        'tahun_id',
        'kategori_id',
        'no',
        'nama_indikator',
        'bobot',
        'keterangan',
    ];

    public function tahun()
    {
        return $this->belongsTo(Tahun::class);
    }

    public function kategori()
    {
        return $this->belongsTo(Kategori::class, 'kategori_id');
    }
}
