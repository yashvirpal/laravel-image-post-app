<?php

use Illuminate\Support\Facades\Route;


use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

Route::get('/image-compose', function () {
    $manager = new ImageManager(new Driver());

    $userimgPath = storage_path('app/public/user/profile/01JZFRXAER8DBD6JQMHAS85GVG.png');
    $evntimgPath = storage_path('app/public/events/01JZDYB6PT66DGDABCM8THJ17W.png');

    if (!file_exists($userimgPath)) {
        abort(404, 'User image not found');
    }
    if (!file_exists($evntimgPath)) {
        abort(404, 'Event image not found');
    }

    // Load event background
    $base = $manager->read($evntimgPath);

    // Load and resize user image to square
    $user = $manager->read($userimgPath)->resize(200, 200);

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
                $color = $user->pickColor($x, $y);
                $circleCanvas->drawPixel($x, $y, $color);
            }
        }
    }

    // Place the circular-cropped user image onto the base event image
    $base->place($circleCanvas, 'top-left', 30, 30);

    // Save final image
    $outputPath = storage_path('app/public/circle-crop11.png');
    $base->save($outputPath, quality: 90, format: 'png');

    return response()->file($outputPath);
});





Route::get('/circle-crop', function () {



    $filePath = storage_path('app/public/user/profile/01JZFRXAER8DBD6JQMHAS85GVG.png');
    $size = 200;

    $manager = new ImageManager(Driver::class); // Correct instantiation

    $image = $manager->read($filePath);

    $image->drawCircle(100, 100, function (CircleFactory $circle) {
        $circle->radius(150); // radius of circle in pixels
        $circle->background('lightblue'); // background color
        $circle->border('b53717', 1); // border color & size
    });
    $encoded = $image->encodeByMediaType('image/gif');
    $image->save(storage_path('app/public/circle-crop.png'));

    return response()->file(storage_path('app/public/circle-crop.png'));
});
