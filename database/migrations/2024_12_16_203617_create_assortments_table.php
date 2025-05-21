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
        Schema::create('assortments', function (Blueprint $table) {
            $table->id();
            $table->string('name');  // Название продукта
            $table->text('description');  // Описание продукта
            $table->decimal('price', 8, 2);  // Цена продукта
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down()
    {
        Schema::dropIfExists('assortments');
    }
};
