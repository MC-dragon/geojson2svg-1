<?php

namespace Geojson2Svg;

class Svg
{
    /**
     * @var string
     */
    protected $template;

    /**
     * @var int
     */
    protected $canvasWidth;

    /**
     * @var int
     */
    protected $canvasHeight;

    /**
     * @var int
     */
    protected $scaleX;

    /**
     * @var int
     */
    protected $scaleY;

    /**
     * @var Polygon[]
     */
    protected $polygons = [];

    /**
     * @var string[]
     */
    protected $texts = [];

    /**
     * @var Bounds
     */
    protected $bounds;

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
            $this->scaleX * $this->bounds->getWidth(),
            $this->scaleY * $this->bounds->getHeight()
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

    /**
     * @return Bounds
     */
    public function getBounds()
    {
        return $this->bounds;
    }

    /**
     * @param Bounds $bounds
     */
    public function setBounds($bounds)
    {
        $this->bounds = $bounds;
    }

    public function getOffsetX()
    {
        $offset = -($this->scaleX * $this->bounds->getXMin());
        return $offset;
    }

    public function getOffsetY()
    {
        $offset = $this->scaleY * $this->bounds->getYMax();
        return $offset;
    }
}
