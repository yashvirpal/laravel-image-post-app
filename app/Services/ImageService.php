<?php

namespace App\Services;

use App\Models\User;
use App\Models\Event;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Encoders\JpegEncoder;
use Intervention\Image\Geometry\Circle;
use Intervention\Image\Drawing\Style;




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
        $profileImagePath = storage_path('app/public/' . $user->profile);
      

        $image = $this->imageManager->read($eventImagePath);

        $profile = $this->imageManager->read($profileImagePath)->resize(100, 100);
        $image->place($profile, 'top-left', 50, 50);


        // Add name
        $image->text("Name: {$user->name}", 100, 100, function ($font) {
            // $font->filename(public_path('fonts/OpenSans-Bold.ttf'));
            $font->size(56);
            $font->color('#FF0000');
        });

        // Add phone
        $image->text("Phone: {$user->phone}", 100, 160, function ($font) {
            // $font->filename(public_path('fonts/OpenSans-Regular.ttf'));
            $font->size(50);
            $font->color('#FF0000');
        });




        $outputPath = "generated/user_{$user->id}_event_{$event->id}_" . time() . ".jpg";

        Storage::disk('public')->put(
            $outputPath,
            $image->encode(new JpegEncoder(quality: 90))
        );

        return Storage::url($outputPath);
    }
}
