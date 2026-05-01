<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GeoArea extends Model
{
    protected $fillable = ['name', 'geojson', 'color', 'kategori'];
}
