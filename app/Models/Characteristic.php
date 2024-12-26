<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Characteristic extends Model
{
    use HasFactory;

    protected $fillable = ['assortment_id', 'name', 'value'];

    // Связь с ассортиментом
    public function assortment()
    {
        return $this->belongsTo(Assortment::class);
    }
}

