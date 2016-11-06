<?php

namespace Geojson2Svg;

use PointReduction\Common\Point;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FeatureRenderer
{
    /**
     * @var string[]
     */
    private $polygons = [];
    /**
     * @var string
     */
    private $text = '';
    /**
     * @var string[]
     */
    private $texts = [];
    /**
     * @var TextRenderer
     */
    private $textRenderer;
    /**
     * @var array
     */
    private $options;

    /**
     * @param TextRenderer|null $textRenderer
     * @param array             $options
     */
    public function __construct($textRenderer, array $options)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);

        if (null !== $textRenderer && !$textRenderer instanceof TextRenderer) {
            throw new \InvalidArgumentException('TextRenderer invalid argument.');
        }
        $this->textRenderer = $textRenderer;
    }

    /**
     * @param OptionsResolver $options
     */
    private function configureOptions(OptionsResolver $options)
    {
        $options->setDefaults([
            'strokeWidth' => 1,
            'fillColor'   => 'black',
            'strokeColor' => 'white',
        ]);
    }

    /**
     * @return \string[]
     */
    public function getPolygons()
    {
        return $this->polygons;
    }

    /**
     * @return \string[]
     */
    public function getTexts()
    {
        return $this->texts;
    }

    /**
     * @param array $feature
     * @param float $scaleX
     * @param float $scaleY
     * @param int   $offsetX
     * @param int   $offsetY
     *
     * @return null
     * @throws \RuntimeException
     */
    public function renderFeature(array $feature, $scaleX, $scaleY, $offsetX, $offsetY)
    {
        $this->polygons = [];
        $this->text = '';
        $this->texts = [];

        $geometry = $feature['geometry'];
        $coordinates = $geometry['coordinates'];

        if (isset($feature['properties']['code'])) {
            $this->text = $feature['properties']['code'];
        }

        switch ($geometry['type']) {
            case 'Polygon':
                $this->renderPolygon($coordinates, $scaleX, $scaleY, $offsetX, $offsetY);

                return null;
                break;
            case 'MultiPolygon':
                foreach ($coordinates as $subCoordinates) {
                    $this->renderPolygon($subCoordinates, $scaleX, $scaleY, $offsetX, $offsetY);
                }

                return null;
        }

        throw new \RuntimeException('Invalid geometry type.');
    }

    /**
     * @param array $coordinates
     * @param float $scaleX
     * @param float $scaleY
     * @param int   $offsetX
     * @param int   $offsetY
     */
    private function renderPolygon(array $coordinates, $scaleX, $scaleY, $offsetX, $offsetY)
    {
        $fillColor = $this->options['fillColor'];
        $strokeColor = $this->options['strokeColor'];
        $strokeWidth = $this->options['strokeWidth'];

        foreach ($coordinates as $subcoordinates) {
            $polygon = new Polygon();

            foreach ($subcoordinates as $coordinate) {
                $x = round($scaleX * $coordinate[0] + $offsetX);
                $y = round(-$scaleY * $coordinate[1] + $offsetY);
                $polygon->addPoint(new Point($x, $y));
            }

            $this->polygons[] = sprintf('<polygon code="%s" style="fill:%s; stroke:%s; stroke-width:%d;" points="%s" />',
                $this->text,
                $strokeColor,
                $fillColor,
                $strokeWidth,
                implode(' ', $polygon->getReducedPoints())
            );

            if (null !== $this->textRenderer && $this->text) {
                $this->texts[] = $this->textRenderer->renderPolygonText($polygon, $this->text);
            }
        }
    }
}
