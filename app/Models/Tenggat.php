<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Tenggat extends Model
{
    protected $fillable = [
        'kategori_id',
        'waktu_aktif',
        'waktu_nonaktif'
    ];

    public function kategori()
    {
        return $this->belongsTo(Kategori::class);
    }

    // status otomatis
    public function getStatusAttribute()
    {
        $now = now();

        if ($now->between($this->waktu_aktif, $this->waktu_nonaktif)) {
            return 'Aktif';
        }

        return 'Tidak Aktif';
    }
}