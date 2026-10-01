<?php

namespace App\Services;

use App\Models\card as LoyaltyCard;
use Illuminate\Support\Facades\Log;
use Spatie\LaravelMobilePass\Enums\PassType;

class WalletStampProgress
{
    public static function circles(int $collected, int $required): string
    {
        $required = max(1, $required);
        $collected = min(max(0, $collected), $required);
        $circles = [];

        for ($index = 0; $index < $required; $index++) {
            $circles[] = $index < $collected ? '●' : '○';
        }

        return implode(' ', $circles);
    }

    public function sync(LoyaltyCard $card): void
    {
        $card->loadMissing(['cardDesign', 'customer']);

        $collected = (int) $card->stamps_collected;
        $required = max(1, (int) $card->cardDesign?->stamps_required);
        $progress = self::circles($collected, $required);
        $balance = "{$collected}/{$required}";
        $customer = $card->customer;

        if (! $customer) {
            return;
        }

        $applePass = $customer->firstApplePass(PassType::StoreCard);

        if ($applePass) {
            try {
                $applePass->builder()
                    ->updateField('progress', $progress)
                    ->updateField('stamps', $balance)
                    ->save();
            } catch (\Throwable $exception) {
                Log::error('Apple Wallet stamp progress update failed.', [
                    'card_id' => $card->id,
                    'exception' => $exception::class,
                ]);
            }
        }

        $googlePass = $customer->firstGooglePass(PassType::StoreCard);

        if ($googlePass) {
            try {
                $content = $googlePass->content;
                $payload = $content['googleObjectPayload'];
                $payload['loyaltyPoints']['balance']['string'] = $balance;
                $modules = collect($payload['textModulesData'] ?? [])
                    ->reject(fn(array $module) => ($module['id'] ?? null) === 'stamp_progress')
                    ->values();
                $modules->push([
                    'header' => 'Progreso',
                    'body' => $progress,
                    'id' => 'stamp_progress',
                ]);
                $payload['textModulesData'] = $modules->all();
                $content['googleObjectPayload'] = $payload;

                $googlePass->update(['content' => $content]);
            } catch (\Throwable $exception) {
                Log::error('Google Wallet stamp progress update failed.', [
                    'card_id' => $card->id,
                    'exception' => $exception::class,
                ]);
            }
        }
    }
}
