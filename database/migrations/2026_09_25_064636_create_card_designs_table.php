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
        Schema::create('card_designs', function (Blueprint $table) {
            $table->id();
            $table->foreignId("team_id")->constrained()->onDelete('cascade');
            $table->boolean('is_active')->default(true);
            $table->json('color_scheme');
            $table->integer('stamps_required');
            $table->string('reward');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('card_designs');
    }
};
