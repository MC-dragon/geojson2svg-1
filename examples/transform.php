<?php

use Geojson2Svg\Converter;
use Geojson2Svg\FeatureRenderer;
use Geojson2Svg\Svg;
use Geojson2Svg\TextRenderer;

require __DIR__ . '/../vendor/autoload.php';

$svg = new Svg(100, 100, 1000, 600);
$textRenderer = new TextRenderer([]);
$featureRenderer = new FeatureRenderer(null, []);
$converter = new Converter($svg, $featureRenderer);

$geojson = __DIR__ . '/../vendor/gregoiredavid/france-geojson/regions.geojson';
$svg = $converter->convert($geojson);
file_put_contents('/tmp/regions.svg', $svg);

$geojson = __DIR__ . '/../vendor/gregoiredavid/france-geojson/departements.geojson';
$svg = $converter->convert($geojson);
file_put_contents('/tmp/departements.svg', $svg);
