<?php

namespace Geojson2Svg;

class Converter
{
    /** @var array */
    protected $options;

    /** @var FeatureRenderer */
    protected $featureRenderer;

    /** @var Svg */
    protected $svg;

    /**
     * @param Svg             $svg
     * @param FeatureRenderer $featureRenderer
     */
    public function __construct(Svg $svg, FeatureRenderer $featureRenderer)
    {
        $this->featureRenderer = $featureRenderer;
        $this->svg = $svg;
    }

    /**
     * @param string $geojsonFile
     *
     * @return Svg
     * @throws \InvalidArgumentException
     */
    public function convert($geojsonFile)
    {
        $features = $this->decodeGeojson($geojsonFile);
        $bounds = $this->computeSvgBounds($features);
        $this->svg->setBounds($bounds);

        $scaleX = $this->svg->getScaleX();
        $scaleY = $this->svg->getScaleY();
        $offsetX = $this->svg->getOffsetX();
        $offsetY = $this->svg->getOffsetY();

        foreach ($features as $feature) {
            if (!$this->isValidFeature($feature)) {
                continue;
            }
            $this->featureRenderer->renderFeature($this->svg, $feature, $scaleX, $scaleY, $offsetX, $offsetY);
            $this->svg->addPolygon(implode('', $this->featureRenderer->getPolygons()));
            $this->svg->addText(implode('', $this->featureRenderer->getTexts()));
        }

        return $this->svg;
    }

    /**
     * @param string $geojsonFile
     *
     * @return mixed[]
     */
    protected function decodeGeojson($geojsonFile)
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

        return $features;
    }

    /**
     * Filter a feature.
     * Can be overriden to filter on properties, values or anything else
     *
     * @param array $feature
     *
     * @return bool
     */
    protected function isValidFeature(array $feature)
    {
        return true;
    }

    /**
     * @param array $features
     *
     * @return Bounds
     */
    protected function computeSvgBounds(array $features)
    {
        $bounds = new Bounds();
        foreach ($features as $feature) {
            if (!$this->isValidFeature($feature)) {
                continue;
            }
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
        if (null === $bounds->getXMax()) {
            throw new \InvalidArgumentException('No polygon found.');
        }

        return $bounds;
    }

    /**
     * @param array  $coordinates
     * @param Bounds $bounds
     *
     * @return Bounds
     */
    protected function registerPolygon(array $coordinates, Bounds $bounds)
    {
        foreach ($coordinates as $subcoordinates) {
            foreach ($subcoordinates as $coordinate) {
                $bounds->addCoordinate($coordinate);
            }
        }

        return $bounds;
    }
}
