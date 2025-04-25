<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('assortments', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('price');
            $table->string('type');
            $table->string('description');
            $table->string('image_url');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('assortments');
    }
};
