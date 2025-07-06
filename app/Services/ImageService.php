<?php

namespace App\Services;

use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
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
        //dd($user, $event);
        // Load event image
        $eventImagePath = storage_path('app/public/' . $event->image);

        $image = $this->imageManager->read($eventImagePath);

        // Add name
        $image->text("Name: {$user->name}", 100, 100, function ($font) {
           // $font->filename(public_path('fonts/OpenSans-Bold.ttf'));
            $font->size(36);
            $font->color('#ffffff');
        });

        // Add phone
        $image->text("Phone: {$user->phone}", 100, 160, function ($font) {
           // $font->filename(public_path('fonts/OpenSans-Regular.ttf'));
            $font->size(24);
            $font->color('#ffffff');
        });

        // Save to public disk
        $outputPath = "public/generated/{$event->id}/user_{$user->id}.jpg";
        Storage::put($outputPath, (string) $image->toJpeg());

        return Storage::url($outputPath); // e.g., /storage/generated/1/user_5.jpg
    }
}
