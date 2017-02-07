<?php

namespace Geojson2Svg;

use Geometry;

class Bounds
{
    /** @var string */
    private $geojson;
    /** @var Geometry */
    private $geometry;
    /** @var float[]
     *array:4 [
     *  "maxy" => 46.139315
     *  "miny" => 45.192547
     *  "maxx" => 0.942595
     *  "minx" => -0.460471
     *]
     */
    private $bbox;

    /**
     * @param string $geojson
     */
    public function __construct($geojson)
    {
        $this->geojson = $geojson;
        $this->geometry = \geoPHP::load($geojson, 'json');
        if (false === $this->geometry) {
            throw new InvalidGeoJsonException(sprintf('Unable to load GeoJSON data'));
        }
        $this->bbox = $this->geometry->getBBox();
    }

    /**
     * @return float
     */
    public function getXMin()
    {
        return $this->bbox['minx'];
    }

    /**
     * @return float
     */
    public function getXMax()
    {
        return $this->bbox['maxx'];
    }

    /**
     * @return float
     */
    public function getYMin()
    {
        return $this->bbox['miny'];
    }

    /**
     * @return float
     */
    public function getYMax()
    {
        return $this->bbox['maxy'];
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->getXMax() - $this->getXMin();
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        return $this->getYMax() - $this->getYMin();
    }
}
