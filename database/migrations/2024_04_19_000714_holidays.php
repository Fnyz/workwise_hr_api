<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        //
        Schema::create('holidays', function (Blueprint $table) {
            $table->id();
            $table->string("holiday");
            $table->string("holiday_start_date");
            $table->string("holiday_end_date");
            $table->string("holiday_year");
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
        Schema::dropIfExists('holidays');
    }
};
