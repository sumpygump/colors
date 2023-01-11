<?php

namespace Colorz;

class Repository
{
    private $file = 'colors.json';
    private $data = array();
    private $preferGrey = true;

    /**
     * An array of hex value to color name
     *
     * @var array
     */
    private $hexIndex = array();

    public function __construct($path)
    {
        $this->file = $path . DIRECTORY_SEPARATOR . $this->file;
        $this->data = json_decode(file_get_contents($this->file));

        $this->createHexIndex();
    }

    private function createHexIndex()
    {
        foreach ($this->data as $colorinfo) {
            // Contains gray
            if ($this->preferGrey && strpos($colorinfo->name, 'gray') !== false) {
                continue;
            }
            if (isset($this->hexIndex[$colorinfo->hex])) {
                $this->hexIndex[$colorinfo->hex] = $this->hexIndex[$colorinfo->hex] . '/' . $colorinfo->name;
            } else {
                $this->hexIndex[$colorinfo->hex] = $colorinfo->name;
            }
        }
    }

    public function findColor($input)
    {
        $color = $this->findColorByName($input);

        if ($color) {
            return $color;
        }

        return $this->findColorByHex($input);
    }

    public function findColorByName($name)
    {
        foreach ($this->data as $colorinfo) {
            if ($colorinfo->name == $name) {
                $color = new Color($colorinfo->hex);
                $color->name = $colorinfo->name;
                return $color;
            }
        }

        return null;
    }

    public function findColorByHex($hex)
    {
        try {
            $color = new Color($hex);
        } catch (\Exception $e) {
            return false;
        }

        $color->name = $this->findNameByHex($color->getCssHex());

        return $color;
    }

    public function getAll($sort = null)
    {
        $colors = array();
        foreach ($this->data as $colorinfo) {
            $color = new Color($colorinfo->hex);
            $color->name = $this->findNameByHex($colorinfo->hex);
            $colors[$color->getHex()] = $color;
        }

        if ($sort) {
            switch ($sort) {
            case 'hsl':
                usort($colors, function ($a, $b) {
                    if ($a->getHue() == $b->getHue()) {
                        if ($a->getSaturation() == $b->getSaturation()) {
                            if ($a->getLightness() == $b->getLightness()) {
                                return 0;
                            } else {
                                return ($a->getLightness() < $b->getLightness()) ? -1 : 1;
                            }
                        } else {
                            return ($a->getSaturation() < $b->getSaturation()) ? -1 : 1;
                        }
                    }
                    return ($a->getHue() < $b->getHue()) ? -1 : 1;
                });
                break;
            case 'hls':
                usort($colors, function ($a, $b) {
                    if ($a->getHue() == $b->getHue()) {
                        if ($a->getLightness() == $b->getLightness()) {
                            if ($a->getSaturation() == $b->getSaturation()) {
                                return 0;
                            } else {
                                return ($a->getSaturation() < $b->getSaturation()) ? -1 : 1;
                            }
                        } else {
                            return ($a->getLightness() < $b->getLightness()) ? -1 : 1;
                        }
                    }
                    return ($a->getHue() < $b->getHue()) ? -1 : 1;
                });
                break;
            case 'lhs':
                usort($colors, function ($a, $b) {
                    if ($a->getLightness() == $b->getLightness()) {
                        if ($a->getHue() == $b->getHue()) {
                            if ($a->getSaturation() == $b->getSaturation()) {
                                return 0;
                            } else {
                                return ($a->getSaturation() < $b->getSaturation()) ? -1 : 1;
                            }
                        } else {
                            return ($a->getHue() < $b->getHue()) ? -1 : 1;
                        }
                    }
                    return ($a->getLightness() < $b->getLightness()) ? -1 : 1;
                });
                break;
            case 'rgbc':
                usort($colors, function ($a, $b) {
                    return ($a->getRgbCalc() < $b->getRgbCalc()) ? -1 : 1;
                });
                break;
            }
        }

        return $colors;
    }

    private static function sortByHue($a, $b)
    {

    }

    public function getRandomColor()
    {
        $colors = $this->data;
        shuffle($colors);

        $colorinfo = reset($colors);
        $color = new Color($colorinfo->hex);
        $color->name = $colorinfo->name;

        return $color;
    }

    public function findNameByHex($hex)
    {
        $hex = strtoupper($hex);

        if (isset($this->hexIndex[$hex])) {
            return $this->hexIndex[$hex];
        }

        return '';
    }

    public function getSpread($mask = 'xx0000')
    {
        $colorObjects = [];
        for ($i = 0; $i <= 255; $i++) {
            $hex = self::formathex($i);
            $color = '#' . str_replace('xx', $hex, $mask);
            $colorObjects[] = $this->findColor($color);
        }

        return $colorObjects;
    }

    public static function formathex($dec)
    {
        if ($dec < 16) {
            return '0' . dechex($dec);
        }

        return dechex($dec);
    }
}
