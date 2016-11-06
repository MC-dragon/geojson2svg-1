<?php

namespace Geojson2Svg;

use Symfony\Component\OptionsResolver\OptionsResolver;

class TextRenderer
{
    /**
     * @var string[]
     */
    private $svgTexts = [];
    /**
     * @var array
     */
    private $options;

    /**
     * @param array $options
     */
    public function __construct(array $options)
    {
        $resolver = new OptionsResolver();
        $this->configureOptions($resolver);
        $this->options = $resolver->resolve($options);
    }

    /**
     * @return string[]
     */
    public function getSvgTexts()
    {
        return $this->svgTexts;
    }

    /**
     * @param string $svgText
     */
    public function addText($svgText)
    {
        $this->svgTexts[] = $svgText;
    }

    public function renderPolygonText(Polygon $polygon, $text)
    {
        // If dx or dy is less than $minXYPixel pixels, don't create label
        $dx = $polygon->getXMax() - $polygon->getXMin();
        $dy = $polygon->getYMax() - $polygon->getYMin();
        if ($dx < $this->options['minXYPixel'] || $dy < $this->options['minXYPixel']) {
            return '';
        }
        // Compute the polygon's center point
        $pointsCount = count($polygon->getReducedPoints());
        $textX = round($polygon->getXSum() / $pointsCount);
        $textY = round($polygon->getYSum() / $pointsCount);

        $textBuffer = sprintf('<text x="%s" y="%s" text-anchor="middle" fill="%s" style="font-size:%s">',
            $textX,
            $textY,
            $this->options['textFill'],
            $this->options['fontSize']
        );

        // If the text is longer than one word, split it one line per word
        $textArray = explode(" ", $text);
        if (count($textArray) > 1) {
            $first = true;
            foreach ($textArray as $textSpan) {
                if ($first) {
                    $textBuffer .= "<tspan>$textSpan</tspan>";
                    $first = false;
                } else {
                    $textBuffer .= sprintf('<tspan x="%s" dy="%d">%s</tspan>',
                        $textX,
                        $this->options['lineSpacing'],
                        $textSpan
                    );
                }
            }
        } else {
            $textBuffer .= $text;
        }

        $textBuffer .= '</text>';

        return $textBuffer;
    }

    /**
     * @param OptionsResolver $options
     */
    private function configureOptions(OptionsResolver $options)
    {
        $options->setDefaults([
            'fontSize'    => 6,
            'lineSpacing' => 8,
            'minXYPixel'  => 15,
            'textFill'    => 'black',
        ]);
    }
}
