<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('card_designs', function (Blueprint $table) {
            $table->string('stamp_icon', 24)->default('star');
            $table->string('stamp_icon_path')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('card_designs', function (Blueprint $table) {
            $table->dropColumn(['stamp_icon', 'stamp_icon_path']);
        });
    }
};
