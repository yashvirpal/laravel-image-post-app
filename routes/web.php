<?php

use Illuminate\Support\Facades\Route;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Geometry\Factories\CircleFactory;


Route::get('/image-compose', function () {
    $manager = new ImageManager(new Driver());

    $userimg = storage_path('app/public/user/profile/01JZFRXAER8DBD6JQMHAS85GVG.png');
    $evntimg = storage_path('app/public/events/01JZDYB6PT66DGDABCM8THJ17W.png');
  
    if (!file_exists($userimg)) {
        abort(404, 'userimg image not found');
    }
    if (!file_exists($evntimg)) {
        abort(404, 'evntimg image not found');
    }


    $img = $manager->read($evntimg);

    $overlay = $manager->read($userimg);

    $overlay->drawCircle(100, 100, function (CircleFactory $circle) {
        $circle->radius(150); // radius of circle in pixels
        // $circle->background('lightblue'); // background color
        $circle->border('b53717', 1); // border color & size
    });

    $img->place($overlay, 'top-left', 30, 30);

    // Output image as PNG respon
    $encoded = $img->encodeByMediaType('image/png');
    $img->save(storage_path('app/public/circle-crop11.png'));

    return response()->file(storage_path('app/public/circle-crop11.png'));
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
