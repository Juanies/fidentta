<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('card_rewards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('customer_id')->constrained('customer_users')->cascadeOnDelete();
            $table->foreignId('card_id')->constrained()->cascadeOnDelete();
            $table->string('reward_name');
            $table->unsignedInteger('cycle_number');
            $table->timestamp('earned_at');
            $table->foreignId('earned_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('delivered_at')->nullable();
            $table->foreignId('delivered_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['card_id', 'cycle_number']);
            $table->index(['team_id', 'delivered_at']);
        });

        DB::table('cards')
            ->join('card_designs', 'card_designs.id', '=', 'cards.card_design_id')
            ->whereColumn('cards.stamps_collected', '>=', 'card_designs.stamps_required')
            ->select([
                'cards.id',
                'cards.team_id',
                'cards.customer_id',
                'cards.updated_at',
                'cards.created_at',
                'card_designs.reward',
            ])
            ->orderBy('cards.id')
            ->chunkById(500, function ($cards): void {
                foreach ($cards as $card) {
                    $timestamp = $card->updated_at ?? $card->created_at ?? now();

                    DB::table('card_rewards')->insert([
                        'team_id' => $card->team_id,
                        'customer_id' => $card->customer_id,
                        'card_id' => $card->id,
                        'reward_name' => $card->reward ?: 'Recompensa',
                        'cycle_number' => 1,
                        'earned_at' => $timestamp,
                        'earned_by' => null,
                        'delivered_at' => null,
                        'delivered_by' => null,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }, 'cards.id');
    }

    public function down(): void
    {
        Schema::dropIfExists('card_rewards');
    }
};
