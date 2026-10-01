<?php

namespace App\Services;

use App\Models\cardDesign as CardDesign;
use App\Models\CustomerRegistrationField;
use App\Models\Location;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class TeamRegistrationService
{
    public function setup(User $user, array $registro): void
    {
        DB::transaction(function () use ($user, $registro) {
            $team = $user->currentTeam;

            if (! $team) {
                return;
            }

            $registration = $registro['registro_usuarios'] ?? [];
            $mode = match ($registration['modo'] ?? 'normal') {
                'none', 'sin_registro' => 'none',
                'normal', 'basico' => 'normal',
                'custom' => 'custom',
                default => 'normal',
            };

            $team->update(['customer_registration_type' => $mode]);

            CardDesign::firstOrCreate(
                ['team_id' => $team->id],
                [
                    'is_active' => true,
                    'color_scheme' => $registro['paleta'] ?? [],
                    'stamps_required' => $registro['sellos'] ?? 8,
                    'reward' => $registro['recompensa'] ?? 'Bebida gratis',
                ],
            );

            Location::firstOrCreate(
                ['team_id' => $team->id],
                [
                    'name' => 'Ubicación principal',
                    'is_active' => true,
                ],
            );

            $configuredFields = match ($mode) {
                'custom' => $registration['campos'] ?? [],
                'normal' => array_intersect($registration['campos'] ?? [], ['telefono']),
                default => [],
            };
            $configuredFields = array_values(array_unique(array_filter($configuredFields, 'is_string')));

            $team->customerRegistrationFields()->update(['is_active' => false]);

            $knownFields = [
                'nombre' => ['Nombre', 'text'],
                'email' => ['Email', 'email'],
                'telefono' => ['Teléfono', 'tel'],
                'cumpleanos' => ['Fecha de cumpleaños', 'date'],
            ];

            foreach ($configuredFields as $position => $configuredField) {
                $fieldKey = Str::slug($configuredField, '_');

                if ($fieldKey === '' || $fieldKey === 'password') {
                    continue;
                }

                [$label, $type] = $knownFields[$fieldKey] ?? [$configuredField, 'text'];

                CustomerRegistrationField::updateOrCreate(
                    ['team_id' => $team->id, 'field_key' => $fieldKey],
                    [
                        'label' => $label,
                        'type' => $type,
                        'is_required' => $mode === 'custom',
                        'sort_order' => $position,
                        'is_active' => true,
                    ],
                );
            }
        });
    }
}
