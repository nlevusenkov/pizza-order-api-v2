<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
    {
        Schema::create('order_items', function (Blueprint $table) {
            $table->id();
            $table->foreignId('order_id')->constrained()->onDelete('cascade'); // Внешний ключ на таблицу orders
            $table->foreignId('assortment_id')->constrained()->onDelete('cascade'); // Внешний ключ на таблицу assortment
            $table->integer('quantity')->default(1); // Количество товаров
            $table->decimal('price', 10, 2); // Цена за единицу товара
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('order_items');
    }
};
