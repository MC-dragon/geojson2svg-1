<?php

use Geojson2Svg\Converter;
use Geojson2Svg\FeatureRenderer;
use Geojson2Svg\Svg;
use Geojson2Svg\TextRenderer;

require __DIR__ . '/../vendor/autoload.php';

$svg = new Svg(100, 130, 500, 500);
$textRenderer = new TextRenderer([]);
$featureRenderer = new FeatureRenderer(null, []);
$converter = new Converter($svg, $featureRenderer);

$geojson = file_get_contents(__DIR__ . '/../vendor/gregoiredavid/france-geojson/regions.geojson');
$svg = $converter->convert($geojson);
file_put_contents('/tmp/regions.svg', $svg);

$geojson = file_get_contents(__DIR__ . '/../vendor/gregoiredavid/france-geojson/departements.geojson');
$svg = $converter->convert($geojson);
file_put_contents('/tmp/departements.svg', $svg);
