<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'total_price'];

    public function items()
    {
        return $this->hasMany(OrderItem::class);
    }

    // Связь с Assortment
    public function assortments()
    {
        return $this->hasMany(OrderItem::class);
    }

}

