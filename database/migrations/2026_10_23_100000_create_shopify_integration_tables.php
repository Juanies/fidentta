<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('shopify_integrations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->unique()->constrained()->cascadeOnDelete();
            $table->string('shop_domain');
            $table->string('webhook_token', 64)->unique();
            $table->text('webhook_secret');
            $table->unsignedSmallInteger('stamps_per_order')->default(1);
            $table->decimal('min_order_amount', 10, 2)->nullable();
            $table->boolean('is_active')->default(true);
            $table->timestamps();
        });

        Schema::create('shopify_orders', function (Blueprint $table) {
            $table->id();
            $table->foreignId('shopify_integration_id')->constrained()->cascadeOnDelete();
            $table->string('shopify_order_id');
            $table->foreignId('card_id')->constrained()->cascadeOnDelete();
            $table->unsignedSmallInteger('stamps_added');
            $table->timestamps();
            $table->unique(['shopify_integration_id', 'shopify_order_id']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('shopify_orders');
        Schema::dropIfExists('shopify_integrations');
    }
};
