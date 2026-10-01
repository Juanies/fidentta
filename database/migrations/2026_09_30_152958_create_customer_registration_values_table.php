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
        Schema::create('customer_registration_values', function (Blueprint $table) {
            $table->id();
            $table->foreignId('customer_user_id')->constrained('customer_users')->cascadeOnDelete();
            $table->foreignId('customer_registration_field_id')->constrained()->cascadeOnDelete();
            $table->text('value')->nullable();
            $table->timestamps();

            $table->unique(['customer_user_id', 'customer_registration_field_id'], 'customer_registration_value_unique');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customer_registration_values');
    }
};
