<?php

namespace App\Services;

use App\Models\card as LoyaltyCard;
use App\Models\Team;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Imagick\Driver as ImagickDriver;
use Intervention\Image\Geometry\Circle;
use Intervention\Image\Geometry\Point;
use Intervention\Image\ImageManager;
use Intervention\Image\Interfaces\ImageInterface;
use Intervention\Image\Interfaces\ImageManagerInterface;
use Throwable;

class StampCardArtwork
{
    /** @return array{strip: ?string, logo: ?string} */
    public function forCard(LoyaltyCard $card): array
    {
        if (! extension_loaded('imagick')) {
            return ['strip' => null, 'logo' => null];
        }

        $card->loadMissing(['cardDesign', 'team']);

        try {
            $manager = ImageManager::usingDriver(ImagickDriver::class);

            return [
                'strip' => $this->makeStampStrip($card, $manager),
                'logo' => $this->makeTeamLogo($card->team, $manager),
            ];
        } catch (Throwable $exception) {
            Log::warning('Wallet card artwork could not be generated.', [
                'card_id' => $card->id,
                'exception' => $exception::class,
            ]);

            return ['strip' => null, 'logo' => null];
        }
    }

    private function makeStampStrip(LoyaltyCard $card, ImageManagerInterface $manager): ?string
    {
        $design = $card->cardDesign;

        if (! $design) {
            return null;
        }

        $required = max(1, min(20, (int) $design->stamps_required));
        $collected = min(max(0, (int) $card->stamps_collected), $required);
        $scheme = $design->color_scheme ?? [];
        $fillColor = $this->hexColor($scheme['colorPrincipal'] ?? '#9EE493', '#9EE493');
        $iconColor = $this->hexColor($scheme['texto'] ?? '#210124', '#210124');
        $image = $manager->createImage(720, 150);

        $usableWidth = 660;
        $cellWidth = $usableWidth / $required;
        $diameter = max(20, min(72, (int) floor($cellWidth * 0.68)));
        $radius = $diameter / 2;
        $centerY = 75;
        $selectedIcon = $design->stamp_icon ?? 'star';
        $customIconPath = $design->stamp_icon_path
            ? storage_path('app/public/' . $design->stamp_icon_path)
            : null;

        for ($index = 0; $index < $required; $index++) {
            $centerX = 30 + ($index + 0.5) * $cellWidth;
            $filled = $index < $collected;
            $circle = new Circle($diameter, new Point((int) round($centerX), $centerY));
            $circle->setBackgroundColor($filled ? $fillColor : 'rgba(255,247,249,0.82)');
            $circle->setBorder($fillColor, 4);
            $image->drawCircle($circle);

            $iconSize = (int) ($diameter * 0.5);
            $icon = $this->makeIcon($selectedIcon, $customIconPath, $iconColor, $iconSize, $manager);

            if ($icon) {
                $image->insert(
                    $icon,
                    (int) round($centerX - ($icon->width() / 2)),
                    (int) round($centerY - ($icon->height() / 2)),
                );
            }
        }

        return $this->writeImage($image, 'stamp-card-' . $card->id . '.png');
    }

    private function makeIcon(
        string $key,
        ?string $customPath,
        string $color,
        int $size,
        ImageManagerInterface $manager,
    ): ?ImageInterface {
        if ($key === 'custom' && $customPath && is_file($customPath)) {
            return $manager->decodePath($customPath)->scaleDown($size, $size);
        }

        $svg = 'data:image/svg+xml;base64,' . base64_encode($this->iconSvg($key, $color, $size));

        return $manager->decodeDataUri($svg)->scaleDown($size, $size);
    }

    private function iconSvg(string $key, string $color, int $size): string
    {
        $symbol = match ($key) {
            'heart' => '<path d="M20.8 8.7c0 5.2-8.8 11-8.8 11S3.2 13.9 3.2 8.7a4.7 4.7 0 0 1 8.8-2.3 4.7 4.7 0 0 1 8.8 2.3Z"/>',
            'coffee' => '<path d="M5 8h12v7a4 4 0 0 1-4 4H9a4 4 0 0 1-4-4V8Zm12 2h1a2 2 0 1 1 0 4h-1M8 4v2m4-2v2m4-2v2M4 22h16"/>',
            'leaf' => '<path d="M20 4c-8 0-14 4-14 11a5 5 0 0 0 5 5c7 0 11-8 9-16ZM4 21c3-5 6-8 11-11"/>',
            default => '<path d="m12 2.8 2.8 5.7 6.3.9-4.6 4.5 1.1 6.3-5.6-3-5.6 3 1.1-6.3-4.6-4.5 6.3-.9L12 2.8Z"/>',
        };

        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $size . '" height="' . $size . '" viewBox="0 0 24 24" fill="none" stroke="' . $color . '" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round">' . $symbol . '</svg>';
    }

    private function makeTeamLogo(Team $team, ImageManagerInterface $manager): ?string
    {
        $logoPath = $team->logo;

        if (! $logoPath) {
            return null;
        }

        if (str_starts_with($logoPath, 'http://') || str_starts_with($logoPath, 'https://')) {
            $urlPath = (string) parse_url($logoPath, PHP_URL_PATH);
            $logoPath = str_starts_with($urlPath, '/storage/')
                ? substr($urlPath, strlen('/storage/'))
                : '';
        }

        $sourcePath = storage_path('app/public/' . ltrim($logoPath, '/'));

        if (! is_file($sourcePath)) {
            return null;
        }

        $source = $manager->decodePath($sourcePath)->scaleDown(520, 140);
        $canvas = $manager->createImage(600, 180);
        $canvas->insert(
            $source,
            (int) round((600 - $source->width()) / 2),
            (int) round((180 - $source->height()) / 2),
        );

        return $this->writeImage($canvas, 'team-logo-' . $team->id . '.png');
    }

    private function writeImage(ImageInterface $image, string $fileName): string
    {
        $directory = storage_path('app/private/passgenerator/generated');

        if (! is_dir($directory)) {
            mkdir($directory, 0750, true);
        }

        $path = $directory . DIRECTORY_SEPARATOR . $fileName;
        $image->save($path);

        return $path;
    }

    private function hexColor(mixed $color, string $fallback): string
    {
        return is_string($color) && preg_match('/^#[0-9A-Fa-f]{6}$/', $color)
            ? $color
            : $fallback;
    }
}
