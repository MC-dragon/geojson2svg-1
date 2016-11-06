<?php

use Geojson2Svg\Converter;
use Geojson2Svg\FeatureRenderer;
use Geojson2Svg\TextRenderer;

require __DIR__ . '/../vendor/autoload.php';

$textRenderer = new TextRenderer([]);
$featureRenderer = new FeatureRenderer($textRenderer, []);
$converter = new Converter($featureRenderer, [
        'canvasWidth'  => 1000,
        'canvasHeight' => 1000,
    ]
);

$geojson = __DIR__ . '/../vendor/gregoiredavid/france-geojson/regions.geojson';
$svg = $converter->convert($geojson);
file_put_contents('/tmp/regions.svg', $svg);

$geojson = __DIR__ . '/../vendor/gregoiredavid/france-geojson/departements.geojson';
$svg = $converter->convert($geojson);
file_put_contents('/tmp/departements.svg', $svg);
