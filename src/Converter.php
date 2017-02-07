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
        $bounds = new Bounds(file_get_contents($geojsonFile));
        $this->svg->setBounds($bounds);

        foreach ($features as $feature) {
            if (!$this->isValidFeature($feature)) {
                continue;
            }
            $this->featureRenderer->renderFeature($this->svg, $feature);
            $this->svg->addPolygon(implode('', $this->featureRenderer->getPolygons()));
            $this->svg->addText(implode('', $this->featureRenderer->getTexts()));
        }

        return $this->svg;
    }

    /**
     * @param string $geojsonFile
     *
     * @return mixed[]
     *
     * @throws \RuntimeException
     * @throws InvalidGeoJsonException
     */
    protected function decodeGeojson($geojsonFile)
    {
        $geojson = file_get_contents($geojsonFile);
        if (false === $geojson) {
            throw new \RuntimeException('Fail to read input file.');
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
