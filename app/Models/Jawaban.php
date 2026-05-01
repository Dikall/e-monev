<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Jawaban extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'public_body_id',
        'pertanyaan_id',
        'tahun_id',
        'jawaban',
        'links',
        'dokumen_path',
        'is_submitted',
        'submitted_at',
    ];

    protected $casts = [
        'links'   => 'array',   // disimpan sebagai JSON di DB
        'jawaban' => 'integer',
        'is_submitted' => 'boolean',
        'submitted_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function publicBody()
    {
        return $this->belongsTo(PublicBody::class);
    }

    public function pertanyaan()
    {
        return $this->belongsTo(Pertanyaan::class);
    }

    public function tahun()
    {
        return $this->belongsTo(Tahun::class);
    }
}