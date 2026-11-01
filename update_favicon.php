<?php

$pngPath = __DIR__ . '/public/assets/admin/images/logo-sm.png';
$pngData = file_get_contents($pngPath);
$b64 = base64_encode($pngData);

$svg = '<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 128 128">'
     . '<image href="data:image/png;base64,' . $b64 . '" width="128" height="128"/>'
     . '</svg>';

file_put_contents(__DIR__ . '/public/favicon.svg', $svg);
file_put_contents(__DIR__ . '/public/favicon.ico', $pngData);
file_put_contents(__DIR__ . '/public/apple-touch-icon.png', $pngData);

echo "Favicons successfully updated with Linkpemda logo!\n";

