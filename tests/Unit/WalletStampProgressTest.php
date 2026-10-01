<?php

use App\Services\WalletStampProgress;

test('stamp progress fills collected circles and leaves remaining circles empty', function () {
    expect(WalletStampProgress::circles(4, 8))->toBe('● ● ● ● ○ ○ ○ ○')
        ->and(WalletStampProgress::circles(0, 8))->toBe('○ ○ ○ ○ ○ ○ ○ ○')
        ->and(WalletStampProgress::circles(12, 8))->toBe('● ● ● ● ● ● ● ●');
});
