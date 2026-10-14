<?php

namespace App\Livewire;

use App\Models\CardReward;
use Livewire\Attributes\On;
use Livewire\Component;

class PendingRewardsBadge extends Component
{
    #[On('rewards-updated')]
    public function refreshPendingCount(): void {}

    public function render()
    {
        $teamId = auth()->user()?->currentTeam?->id;
        $pendingRewardsCount = $teamId
            ? CardReward::query()->where('team_id', $teamId)->whereNull('delivered_at')->count()
            : 0;

        return view('livewire.pending-rewards-badge', [
            'pendingRewardsCount' => $pendingRewardsCount,
        ]);
    }
}
