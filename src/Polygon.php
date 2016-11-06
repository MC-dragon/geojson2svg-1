<?php

namespace Geojson2Svg;

use PointReduction\Algorithms\RamerDouglasPeucker;
use PointReduction\Common\PointInterface;

class Polygon
{
    /**
     * @var PointInterface[]
     */
    private $points = [];
    /**
     * @var PointInterface[]
     */
    private $reducedPoints = null;
    /**
     * @var int
     */
    private $xMax = 0;
    /**
     * @var int
     */
    private $xMin = 0;
    /**
     * @var int
     */
    private $xSum = 0;
    /**
     * @var int
     */
    private $yMax = 0;
    /**
     * @var int
     */
    private $yMin = 0;
    /**
     * @var int
     */
    private $ySum = 0;

    /**
     * @return \PointReduction\Common\PointInterface[]
     */
    public function getPoints()
    {
        return $this->points;
    }

    /**
     * @param \PointReduction\Common\PointInterface[] $points
     */
    public function setPoints($points)
    {
        $this->points = $points;
    }

    /**
     * @param \PointReduction\Common\Point $point
     */
    public function addPoint($point)
    {
        $this->points[] = $point;
        if ($point->x < $this->xMin) {
            $this->xMin = $point->x;
        }
        if ($point->x > $this->xMax) {
            $this->xMax = $point->x;
        }
        if ($point->y < $this->yMin) {
            $this->yMin = $point->y;
        }
        if ($point->y > $this->yMax) {
            $this->yMax = $point->y;
        }
        $this->xSum += $point->x;
        $this->ySum += $point->y;
    }

    /**
     * @return int
     */
    public function getXMax()
    {
        return $this->xMax;
    }

    /**
     * @param int $xMax
     */
    public function setXMax($xMax)
    {
        $this->xMax = $xMax;
    }

    /**
     * @return int
     */
    public function getXMin()
    {
        return $this->xMin;
    }

    /**
     * @param int $xMin
     */
    public function setXMin($xMin)
    {
        $this->xMin = $xMin;
    }

    /**
     * @return int
     */
    public function getXSum()
    {
        return $this->xSum;
    }

    /**
     * @param int $xSum
     */
    public function setXSum($xSum)
    {
        $this->xSum = $xSum;
    }

    /**
     * @return int
     */
    public function getYMax()
    {
        return $this->yMax;
    }

    /**
     * @param int $yMax
     */
    public function setYMax($yMax)
    {
        $this->yMax = $yMax;
    }

    /**
     * @return int
     */
    public function getYMin()
    {
        return $this->yMin;
    }

    /**
     * @param int $yMin
     */
    public function setYMin($yMin)
    {
        $this->yMin = $yMin;
    }

    /**
     * @return int
     */
    public function getYSum()
    {
        return $this->ySum;
    }

    /**
     * @param int $ySum
     */
    public function setYSum($ySum)
    {
        $this->ySum = $ySum;
    }

    public function reduce()
    {
        $epsilon = 1;
        $reducer = new RamerDouglasPeucker($this->points);
        $this->reducedPoints = $reducer->reduce($epsilon);

        return $this->reducedPoints;
    }

    public function getReducedPoints()
    {
        if (null === $this->reducedPoints) {
            $this->reduce();
        }

        return $this->reducedPoints;
    }
}
