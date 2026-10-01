<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('mobile_passes', function (Blueprint $table) {
            $table->timestamp('wallet_added_at')->nullable();
            $table->timestamp('wallet_removed_at')->nullable();
        });
    }

    public function down(): void
    {
        Schema::table('mobile_passes', function (Blueprint $table) {
            $table->dropColumn(['wallet_added_at', 'wallet_removed_at']);
        });
    }
};
