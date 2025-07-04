<?php
require 'vendor/autoload.php';

use Intervention\Image\ImageManager;

// WhatsApp API credentials (UltraMsg)
$instanceId = 'YOUR_INSTANCE_ID';
$token = 'YOUR_API_TOKEN';

$manager = new ImageManager(['driver' => 'gd']);

$users = [
    ['name' => 'Raj', 'phone' => '+911234567890'],
    ['name' => 'Anita', 'phone' => '+919876543210'],
];

$baseImage = __DIR__ . '/base.jpg';
$outputDir = __DIR__ . '/output/';

if (!file_exists($outputDir)) {
    mkdir($outputDir, 0777, true);
}

foreach ($users as $user) {
    $image = $manager->make($baseImage);

    $image->text("Dear {$user['name']}", 100, 100, function ($font) {
        $font->file(__DIR__ . '/font/OpenSans-VariableFont_wdth,wght.ttf');
        $font->size(148);
        $font->color('#FF0000');
        $font->align('left');
        $font->valign('top');
    });

    $fileName = 'personalized_' . strtolower($user['name']) . '_' . time() . '.jpg';
    $filePath = $outputDir . $fileName;
    $image->save($filePath);

    echo "✅ Created image for {$user['name']}." . PHP_EOL;

    $publicUrl = 'https://yourdomain.com/output/' . $fileName;

    //$response = sendWhatsAppImage($user['phone'], $publicUrl, "Happy Festival, {$user['name']}!", $instanceId, $token);

   // echo "📤 WhatsApp sent to {$user['phone']}: {$response}" . PHP_EOL;
}

function sendWhatsAppImage($to, $imageUrl, $caption, $instanceId, $token) {
    $url = "https://api.ultramsg.com/$instanceId/messages/image";

    $data = [
        "to" => $to,
        "image" => $imageUrl,
        "caption" => $caption
    ];

    $headers = [
        "Content-Type: application/json",
        "Authorization: Bearer $token"
    ];

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
    $result = curl_exec($ch);
    curl_close($ch);

    return $result;
}
