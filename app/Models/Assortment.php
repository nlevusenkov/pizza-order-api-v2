<?php

namespace App\Models;

use App\Models\Characteristic;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assortment extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price'];

    // Связь с характеристиками
    public function characteristics()
    {
        return $this->hasMany(Characteristic::class);
    }

}
