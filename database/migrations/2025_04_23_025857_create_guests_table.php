<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{

    public function up(): void
    {
        Schema::create('guests', function (Blueprint $table) {
            $table->id();
            $table->string('guest_id');
            $table->string('ip');
            $table->timestamps();
        });
    }


    public function down(): void
    {
        Schema::dropIfExists('guests');
    }
};
