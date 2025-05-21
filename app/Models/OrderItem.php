<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OrderItem extends Model
{
    use HasFactory;

    protected $fillable = ['order_id', 'assortment_id', 'quantity', 'price'];
    // Связь с моделью Assortment (один элемент заказа принадлежит одному товару)
    public function assortment()
    {
        return $this->belongsTo(Assortment::class);
    }
    public function order()
    {
        return $this->belongsTo(Order::class);
    }

}
