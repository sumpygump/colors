<?php

namespace Colorz;

use Mexitek\PHPColors\Color as MColor;

class Color extends MColor
{
    public $name = '';

    public function getCsshex()
    {
        return '#' . $this->getHex();
    }

    public function printHsl()
    {
        $hsl = $this->getHsl();
        return sprintf(
            "HSL: %s° %s%% %s%%",
            round($hsl['H'], 2),
            round($hsl['S'] * 100, 2),
            round($hsl['L'] * 100, 2)
        );
    }

    /**
     * Get Hue component from HSL
     *
     * @return float
     */
    public function getHue()
    {
        $hsl = $this->getHsl();
        return $hsl['H'];
    }

    /**
     * Get Saturation component from HSL
     *
     * @return float
     */
    public function getSaturation()
    {
        $hsl = $this->getHsl();
        return $hsl['S'];
    }

    /**
     * Get Lightness component from HSL
     *
     * @return float
     */
    public function getLightness()
    {
        $hsl = $this->getHsl();
        return $hsl['L'];
    }

    public function getRgbCalc()
    {
        // Get our color
        $color = $this->getHex();

        // Calculate straight from rbg
        $r = hexdec($color[0].$color[1]);
        $g = hexdec($color[2].$color[3]);
        $b = hexdec($color[4].$color[5]);

        return ($r*299 + $g*587 + $b*114) / 1000;
    }

    public function getRpercent()
    {
        $color = $this->getHex();
        $r = hexdec($color[0].$color[1]);

        return $r / 255;
    }

    public function getGpercent()
    {
        $color = $this->getHex();
        $g = hexdec($color[2].$color[3]);

        return $g / 255;
    }

    public function getBpercent()
    {
        $color = $this->getHex();
        $b = hexdec($color[4].$color[5]);

        return $b / 255;
    }
}
