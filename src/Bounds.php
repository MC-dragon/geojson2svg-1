<?php

namespace Geojson2Svg;

class Bounds
{
    /** @var float */
    private $xMin;
    /** @var float */
    private $xMax;
    /** @var float */
    private $yMin;
    /** @var float */
    private $yMax;

    /**
     * @return float
     */
    public function getXMin()
    {
        return $this->xMin;
    }

    /**
     * @return float
     */
    public function getXMax()
    {
        return $this->xMax;
    }

    /**
     * @return float
     */
    public function getYMin()
    {
        return $this->yMin;
    }

    /**
     * @return float
     */
    public function getYMax()
    {
        return $this->yMax;
    }

    /**
     * @return float
     */
    public function getWidth()
    {
        return $this->xMax - $this->xMin;
    }

    /**
     * @return float
     */
    public function getHeight()
    {
        return $this->yMax - $this->yMin;
    }

    /**
     * @param float[] $coordinate
     */
    public function addCoordinate(array $coordinate)
    {
        if (!isset($this->xMin)) {
            $this->xMin = $coordinate[0];
        } else {
            if ($coordinate[0] < $this->xMin) {
                $this->xMin = $coordinate[0];
            }
        }
        if (!isset($this->xMax)) {
            $this->xMax = $coordinate[0];
        } else {
            if ($coordinate[0] > $this->xMax) {
                $this->xMax = $coordinate[0];
            }
        }

        if (!isset($this->yMin)) {
            $this->yMin = $coordinate[1];
        } else {
            if ($coordinate[1] < $this->yMin) {
                $this->yMin = $coordinate[1];
            }
        }
        if (!isset($this->yMax)) {
            $this->yMax = $coordinate[1];
        } else {
            if ($coordinate[1] > $this->yMax) {
                $this->yMax = $coordinate[1];
            }
        }
    }
}
