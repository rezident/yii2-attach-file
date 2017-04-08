<?php


namespace rezident\attachfile\views;


use rezident\attachfile\views\extra\image\ImageResizer;
use rezident\attachfile\views\extra\image\ImageResizerProportional;

abstract class ImageView extends AbstractView
{
    const QUALITY_DEFAULT = 75;

    /**
     * @var int
     */
    public $width;

    /**
     * @var int
     */
    public $height;

    /**
     * @var int
     */
    public $resizeMode = ImageResizer::RESIZE_MODE_PAD;

    /**
     * @var int
     */
    public $anchor = ImageResizer::ANCHOR_CENTER_CENTER;

    /**
     * @var string
     */
    public $padColor = ImageResizer::PAD_COLOR_BLACK;

    /**
     * @var int
     */
    public $quality = self::QUALITY_DEFAULT;

    /**
     * Sets the width of the output image
     *
     * @param int $width
     *
     * @return $this
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function width($width)
    {
        $this->width = $width;
        return $this;
    }

    /**
     * Sets the height of the output image
     *
     * @param int $height
     *
     * @return $this
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function height($height)
    {
        $this->height = $height;
        return $this;
    }

    /**
     * Sets the resize mode of the output image
     *
     * @param int $resizeMode
     *
     * @return $this
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function resizeMode($resizeMode)
    {
        $this->resizeMode = $resizeMode;
        return $this;
    }

    /**
     * Sets the anchor of the output image
     *
     * @param int $anchor
     *
     * @return $this
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function anchor($anchor)
    {
        $this->anchor = $anchor;
        return $this;
    }

    /**
     * Sets the pad color of the output image
     *
     * @param string $padColor
     *
     * @return $this
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function padColor($padColor)
    {
        $this->padColor = $padColor;
        return $this;
    }

    /**
     * Sets the quality of the output image (0-100)
     *
     * @param int $quality
     *
     * @return $this
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function quality($quality)
    {
        $this->quality = $quality;
        return $this;
    }

    /**
     * @inheritdoc
     */
    public function render()
    {
        $image = $this->getOriginalImage();
        if($image) {
            imagesavealpha($image, true);
            if($this->isSizeSpecified()) {
                $imageResizer = ImageResizer::create($this->width, $this->height, $this->resizeMode, $this->anchor, $this->padColor);
                $image = $imageResizer->getResizedImage($image);
            }

            $this->output($image);
        }
    }

    /**
     * Renders an image
     *
     * @param resource $image
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    abstract protected function output($image);


    /**
     * @return null|resource
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function getOriginalImage()
    {
        $mime = $this->attachedFile->getMimeType();
        if(mb_strpos($mime, 'png')) {
            return imagecreatefrompng($this->attachedFile->getOriginalFilePath());
        }

        if(mb_strpos($mime, 'jpeg')) {
            return imagecreatefromjpeg($this->attachedFile->getOriginalFilePath());
        }

        if(mb_strpos($mime, 'gif')) {
            return imagecreatefromgif($this->attachedFile->getOriginalFilePath());
        }

        $resource = @imagecreatefrompng($this->attachedFile->getOriginalFilePath());
        $resource ?: @imagecreatefromjpeg($this->attachedFile->getOriginalFilePath());
        $resource ?: @imagecreatefromgif($this->attachedFile->getOriginalFilePath());

        return $resource ?: null;
    }

    /**
     * @return bool
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function isSizeSpecified()
    {
        return $this->width || $this->height;
    }
}