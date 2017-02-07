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
     * @var string
     */
    protected $template = '<polygon fill="%s" stroke="%s" stroke-width="%s" points="%s" />';

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
            'strokeWidth' => '1px',
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
     * @param Svg   $svg
     * @param array $feature
     *
     * @return null
     */
    public function renderFeature(Svg $svg, array $feature)
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
                $this->addPolygons($svg, $coordinates);

                return null;
                break;
            case 'MultiPolygon':
                foreach ($coordinates as $subCoordinates) {
                    $this->addPolygons($svg, $subCoordinates);
                }

                return null;
        }

        throw new \RuntimeException('Invalid geometry type.');
    }

    /**
     * @param Svg   $svg
     * @param array $coordinates
     */
    protected function addPolygons(Svg $svg, array $coordinates)
    {
        foreach ($coordinates as $subcoordinates) {
            $polygon = new Polygon();

            foreach ($subcoordinates as $coordinate) {
                $x = $svg->getScaleX() * $coordinate[0] + $svg->getOffsetX();
                $y = -$svg->getScaleY() * $coordinate[1] + $svg->getOffsetY();
                $polygon->addPoint(new Point($x, $y));
            }

            $this->polygons[] = $this->renderPolygon($polygon);

            if (null !== $this->textRenderer && $this->text) {
                $this->texts[] = $this->textRenderer->renderPolygonText($polygon, $this->text);
            }
        }
    }

    protected function renderPolygon(Polygon $polygon)
    {
        $fillColor = $this->options['fillColor'];
        $strokeColor = $this->options['strokeColor'];
        $strokeWidth = $this->options['strokeWidth'];

        return sprintf($this->template,
            $strokeColor,
            $fillColor,
            $strokeWidth,
            implode(' ', $polygon->getReducedPoints())
        );
    }
}
