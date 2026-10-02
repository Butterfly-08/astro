<?php

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\Models\Product;

$dir = __DIR__ . '/storage/app/public/products';

if (!is_dir($dir)) {
    mkdir($dir, 0777, true);
}

$products = Product::orderBy('id')->get();

foreach ($products as $product) {
    $slug = $product->slug;
    $name = htmlspecialchars($product->name, ENT_QUOTES, 'UTF-8');
    $category = htmlspecialchars($product->category?->name ?? 'Spiritual Product', ENT_QUOTES, 'UTF-8');

    $colors = [
        '#6A1B9A','#8E44AD','#B9770E','#AF601A','#1B4F72',
        '#117864','#922B21','#7D3C98','#CA6F1E','#196F3D'
    ];

    $bg = $colors[$product->id % count($colors)];

    $svg = <<<SVG
<svg xmlns="http://www.w3.org/2000/svg" width="800" height="800" viewBox="0 0 800 800">
<defs>
 <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
  <stop offset="0%" stop-color="$bg"/>
  <stop offset="100%" stop-color="#F5D76E"/>
 </linearGradient>
</defs>
<rect width="800" height="800" fill="#FAF7F0"/>
<rect x="35" y="35" width="730" height="730" rx="40" fill="url(#g)"/>
<circle cx="400" cy="330" r="150" fill="#FFF8E7" opacity=".95"/>
<circle cx="400" cy="330" r="110" fill="none" stroke="$bg" stroke-width="12"/>
<circle cx="400" cy="330" r="55" fill="#F5D76E"/>
<path d="M400 235 L420 300 L490 300 L435 340 L455 405 L400 365 L345 405 L365 340 L310 300 L380 300 Z"
      fill="$bg"/>
<text x="400" y="555" text-anchor="middle"
      font-family="Arial, sans-serif" font-size="32" font-weight="700"
      fill="#24142F">$name</text>
<text x="400" y="600" text-anchor="middle"
      font-family="Arial, sans-serif" font-size="20"
      fill="#4A3A50">$category</text>
<text x="400" y="680" text-anchor="middle"
      font-family="Arial, sans-serif" font-size="18"
      fill="#6B5B73">AstroVaani • Spiritual Collection</text>
</svg>
SVG;

    $file = $dir . '/' . $slug . '.svg';
    file_put_contents($file, $svg);

    $product->update([
        'image' => 'products/' . $slug . '.svg'
    ]);
}

echo "Created unique images for " . $products->count() . " products.\n";
