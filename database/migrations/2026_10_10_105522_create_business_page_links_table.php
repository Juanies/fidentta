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
        Schema::create('business_page_links', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->cascadeOnDelete();
            $table->foreignId('location_id')->cascadeOnDelete();
                $table->string('url');
                $table->string('icon')->nullable();
                $table->string('name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('business_page_links');
    }
};
