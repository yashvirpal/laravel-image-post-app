<?php

namespace App\Services;

use App\Models\User;
use App\Models\Event;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class ImageService
{
    protected ImageManager $imageManager;

    public function __construct()
    {
        // Pass the GD driver explicitly
        $this->imageManager = new ImageManager(new Driver());
    }

    public function generate(User $user, Event $event): string
    {
        $evntimgPath = storage_path('app/public/' . $event->image);
        $userimgPath = storage_path('app/public/' . $user->profile);

        $manager = new ImageManager(new Driver());

        if (!file_exists($userimgPath)) {
            abort(404, 'User image not found');
        }
        if (!file_exists($evntimgPath)) {
            abort(404, 'Event image not found');
        }

        // Load event background
        $base = $manager->read($evntimgPath);

        // Load and resize user image to square
        $userimgPath = $manager->read($userimgPath)->resize(200, 200);

        // Create a transparent canvas
        $circleCanvas = $manager->create(200, 200)->fill('rgba(0,0,0,0)');

        // Manual pixel-copy circular crop (GD only workaround)
        $centerX = 100;
        $centerY = 100;
        $radius = 100;

        for ($y = 0; $y < 200; $y++) {
            for ($x = 0; $x < 200; $x++) {
                $dx = $x - $centerX;
                $dy = $y - $centerY;
                if (($dx * $dx + $dy * $dy) <= ($radius * $radius)) {
                    $color = $userimgPath->pickColor($x, $y);
                    $circleCanvas->drawPixel($x, $y, $color);
                }
            }
        }
        // Place the circular-cropped user image onto the base event image
        $base->place($circleCanvas, 'top-left', 30, 30);

        // Save final image
        $outputPath = storage_path("app/public/generated/{$user->id}_{$user->name}_event_{$event->name}_" . time() . ".png");
        //$outputPath = storage_path('app/public/circle-crop111.png');
        $base->save($outputPath, quality: 90, format: 'png');

        return response()->file($outputPath);

    }
}
