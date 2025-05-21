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
        Schema::create('characteristics', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assortment_id')->constrained()->onDelete('cascade');  // Связь с ассортиментом
            $table->string('name');  // Название характеристики
            $table->string('value');  // Значение характеристики
            $table->timestamps();
        });
    }


    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('characteristics');
    }
};
