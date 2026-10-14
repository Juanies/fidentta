<span class="contents">
    @if ($pendingRewardsCount > 0)
        <span aria-label="{{ $pendingRewardsCount }} recompensas pendientes" title="{{ $pendingRewardsCount }} pendientes"
            class="ml-auto inline-flex min-w-5 items-center justify-center rounded-full bg-green-600 px-1.5 py-0.5 text-[11px] font-bold leading-none text-white">
            {{ $pendingRewardsCount > 99 ? '99+' : $pendingRewardsCount }}
        </span>
    @endif
</span>
