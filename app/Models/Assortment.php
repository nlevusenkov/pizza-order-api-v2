<?php

namespace App\Models;


use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Assortment extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'price'];

    // Добавляем связь с характеристиками
    public function characteristics()
    {
        return $this->hasMany(Characteristic::class, 'assortment_id');
    }


}
