<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('cards', function (Blueprint $table) {
            $table->string('referral_token', 40)->nullable()->unique();
        });

        DB::table('cards')->whereNull('referral_token')->orderBy('id')->each(function ($card): void {
            DB::table('cards')->where('id', $card->id)->update(['referral_token' => Str::random(32)]);
        });

        Schema::table('card_transactions', function (Blueprint $table) {
            $table->foreignId('user_id')->nullable()->change();
        });

        Schema::create('referrals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('team_id')->constrained()->cascadeOnDelete();
            $table->foreignId('referrer_card_id')->constrained('cards')->cascadeOnDelete();
            $table->foreignId('referred_card_id')->unique()->constrained('cards')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('referrals');

        Schema::table('cards', function (Blueprint $table) {
            $table->dropUnique(['referral_token']);
            $table->dropColumn('referral_token');
        });
    }
};
