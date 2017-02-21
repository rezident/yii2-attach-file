<?php


namespace rezident\attachfile\views;


class ImageView extends RawView
{
    const FORMAT_JPEG = 'jpg';

    const FORMAT_PNG = 'png';

    /**
     * Width of the image
     * @var int
     */
    private $width;

    /**
     * Height of the image
     * @var int
     */
    private $height;

    /**
     * Quality of the output image
     * @var float
     */
    private $quality = 0.75;

    /**
     * Format of the output image
     * @var string
     */
    private $format = self::FORMAT_JPEG;

    public function width($width)
    {
        $this->width = $width;
        return $this;
    }

    public function height($height)
    {
        $this->height = $height;
        return $this;
    }

    public function getWidth()
    {
        return $this->width;
    }

    public function getHeight()
    {
        return $this->height;
    }

    public function getSettingsArray()
    {
        return [$this->width, $this->height];
    }

    public function setSettingsArray(array $settings)
    {
        if(!empty($settings[0])) {
            $this->width = $settings[0];
        }

        if(!empty($settings[1])) {
            $this->height = $settings[1];
        }
    }

    public function getOutputExtension()
    {
        return $this->format;
    }
}