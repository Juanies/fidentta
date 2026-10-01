<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customer_users')->cascadeOnDelete();
        });

        Schema::table('card_transactions', function (Blueprint $table) {
            $table->foreign('customer_id')->references('id')->on('customer_users')->cascadeOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('card_transactions', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });

        Schema::table('cards', function (Blueprint $table) {
            $table->dropForeign(['customer_id']);
        });
    }
};
