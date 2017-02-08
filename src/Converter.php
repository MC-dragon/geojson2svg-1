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
     * @param string $geojson
     *
     * @return Svg
     * @throws InvalidGeoJsonException
     */
    public function convert($geojson)
    {
        $features = $this->decodeGeojson($geojson);
        $geometry = \geoPHP::load($geojson, 'json');
        if (false === $geometry) {
            throw new InvalidGeoJsonException(sprintf('Unable to load GeoJSON data'));
        }
        $this->svg->setGeometry($geometry);

        foreach ($features as $feature) {
            if (!$this->isValidFeature($feature)) {
                continue;
            }
            $this->featureRenderer->renderFeature($this->svg, $feature);
            $this->svg->addPolygon(implode('', $this->featureRenderer->getPolygons()));
            $this->svg->addText(implode('', $this->featureRenderer->getTextPolygons()));
        }

        return $this->svg;
    }

    /**
     * @param string $geojson
     *
     * @return mixed[]
     *
     * @throws InvalidGeoJsonException
     */
    protected function decodeGeojson($geojson)
    {
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
                throw new InvalidGeoJsonException('Unsupported GeoJSON format.');
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
}
