<?php

namespace Geojson2Svg;

use Geometry;

class Svg
{
    /** @var string */
    protected $template;

    /** @var int */
    protected $canvasWidth;

    /** @var int */
    protected $canvasHeight;

    /** @var int */
    protected $scaleX;

    /** @var int */
    protected $scaleY;

    /** @var Polygon[] */
    protected $polygons = [];

    /** @var string[] */
    protected $texts = [];

    /** @var Geometry */
    private $geometry;

    /**
     * boundaries box
     *
     * @var float[]
     *
     *  array:4 [
     *    "maxy" => 46.139315
     *    "miny" => 45.192547
     *    "maxx" => 0.942595
     *    "minx" => -0.460471
     *  ]
     */
    private $bbox;

    /**
     * @param int $scaleX
     * @param int $scaleY
     * @param int $canvasWidth  (pixels)
     * @param int $canvasHeight (pixels)
     */
    public function __construct($scaleX, $scaleY, $canvasWidth, $canvasHeight)
    {
        $this->template = '<svg xmlns="http://www.w3.org/2000/svg" version="1.2" 
            width="%d" height="%d" 
            viewBox="%s">%s%s</svg>';
        $this->scaleX = $scaleX;
        $this->scaleY = $scaleY;
        $this->canvasWidth = $canvasWidth;
        $this->canvasHeight = $canvasHeight;
    }

    public function __toString()
    {
        $viewBox = sprintf('0 0 %d %d',
            $this->scaleX * $this->getWidth(),
            $this->scaleY * $this->getHeight()
        );

        return sprintf($this->template,
            $this->canvasWidth,
            $this->canvasHeight,
            $viewBox,
            implode(PHP_EOL, $this->polygons),
            implode(PHP_EOL, $this->texts)
        );
    }

    /**
     * @param string $template
     */
    public function setTemplate($template)
    {
        $this->template = $template;
    }

    /**
     * @param Polygon $polygon
     */
    public function addPolygon($polygon)
    {
        $this->polygons[] = $polygon;
    }

    /**
     * @param string $text
     */
    public function addText($text)
    {
        $this->texts[] = $text;
    }

    /**
     * @return int
     */
    public function getScaleX()
    {
        return $this->scaleX;
    }

    /**
     * @return int
     */
    public function getScaleY()
    {
        return $this->scaleY;
    }

    public function getOffsetX()
    {
        $offset = -($this->scaleX * $this->getXMin());

        return $offset;
    }

    public function getOffsetY()
    {
        $offset = $this->scaleY * $this->getYMax();

        return $offset;
    }

    /**
     * @param Geometry $geometry
     */
    public function setGeometry($geometry)
    {
        $this->geometry = $geometry;
        $this->bbox = $geometry->getBBox();
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
