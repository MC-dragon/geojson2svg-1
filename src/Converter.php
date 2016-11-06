<?php

namespace Geojson2Svg;

use Symfony\Component\OptionsResolver\OptionsResolver;

class Converter
{
    /** @var array */
    private $options;

    /** @var FeatureRenderer */
    private $featureRenderer;

    /**
     * @param FeatureRenderer $featureRenderer
     * @param array           $options
     */
    public function __construct(FeatureRenderer $featureRenderer, array $options)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
        $this->featureRenderer = $featureRenderer;
    }

    /**
     * @param string $geojsonFile
     *
     * @return string
     * @throws \InvalidArgumentException
     */
    public function convert($geojsonFile)
    {
        $geojson = file_get_contents($geojsonFile);
        if (false === $geojson) {
            throw new \InvalidArgumentException('Fail to read input file.');
        }

        $object = json_decode($geojson, true);
        if (isset($object['type'])) {
            if ($object['type'] === 'FeatureCollection') {
                $features = $object['features'];
            } else {
                $features = [$object];
            }
        } else {
            if (gettype($object) === 'array' &&
                isset($object[0]['type']) &&
                $object[0]['type'] === 'Feature'
            ) {
                $features = $object;
            } else {
                throw new \InvalidArgumentException('Unsupported GeoJSON format.');
            }
        }

        $bounds = [];
        foreach ($features as $feature) {
            $geometry = $feature['geometry'];
            $coordinates = $geometry['coordinates'];
            switch ($geometry['type']) {
                case 'Polygon':
                    $bounds = $this->registerPolygon($coordinates, $bounds);
                    break;
                case 'MultiPolygon':
                    foreach ($coordinates as $polygon) {
                        $bounds = $this->registerPolygon($polygon, $bounds);
                    }
            }
        }
        // Check if the geojson doesn't contain any single polygon or multipolygon
        if (!isset($bounds['maxX'])) {
            throw new \InvalidArgumentException('No polygon found.');
        }

        $width = $bounds['maxX'] - $bounds['minX'];
        $height = $bounds['maxY'] - $bounds['minY'];
        $scaleX = $this->options['canvasWidth'] / $width;
        $scaleY = $this->options['canvasHeight'] / $height;

        $offsetX = $this->options['left'] - ($scaleX * $bounds['minX']);
        $offsetY = $this->options['top'] + ($scaleY * $bounds['maxY']);

        $polygons = [];
        $texts = [];

        foreach ($features as $feature) {
            $this->featureRenderer->renderFeature($feature, $scaleX, $scaleY, $offsetX, $offsetY);
            $polygons[] = implode('', $this->featureRenderer->getPolygons());
            $texts[] = implode('', $this->featureRenderer->getTexts());
        }

        return sprintf('<svg width="%d" height="%d">%s%s</svg>',
            round($scaleX * $width),
            round($scaleY * $height),
            implode('', $polygons),
            implode('', $texts)
        );
    }

    /**
     * @param string $svgfile Output filename
     * @param string $content SVG content string
     *
     * @throws \RuntimeException
     */
    public function saveSvg($svgfile, $content)
    {
        $bytes = file_put_contents($svgfile, $content);
        if (0 == $bytes) {
            throw new \RuntimeException(sprintf('No content written into %s.', $svgfile));
        }
    }

    /**
     * @param OptionsResolver $options
     */
    private function configureOptions(OptionsResolver $options)
    {
        $options->setDefaults([
            'top'  => 0,
            'left' => 0,
        ]);
        $options->setDefined([
            'canvasWidth',
            'canvasHeight',
        ]);
    }

    /**
     * @param array $coordinates
     * @param array $bounds
     *
     * @return array new bounds
     */
    private function registerPolygon(array $coordinates, array $bounds)
    {
        foreach ($coordinates as $subcoordinates) {
            foreach ($subcoordinates as $coordinate) {
                if (!isset($bounds['minX'])) {
                    $bounds['minX'] = $coordinate[0];
                } else {
                    if ($coordinate[0] < $bounds['minX']) {
                        $bounds['minX'] = $coordinate[0];
                    }
                }
                if (!isset($bounds['maxX'])) {
                    $bounds['maxX'] = $coordinate[0];
                } else {
                    if ($coordinate[0] > $bounds['maxX']) {
                        $bounds['maxX'] = $coordinate[0];
                    }
                }

                if (!isset($bounds['minY'])) {
                    $bounds['minY'] = $coordinate[1];
                } else {
                    if ($coordinate[1] < $bounds['minY']) {
                        $bounds['minY'] = $coordinate[1];
                    }
                }
                if (!isset($bounds['maxY'])) {
                    $bounds['maxY'] = $coordinate[1];
                } else {
                    if ($coordinate[1] > $bounds['maxY']) {
                        $bounds['maxY'] = $coordinate[1];
                    }
                }
            }
        }

        return $bounds;
    }
}
