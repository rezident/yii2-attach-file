<?php


namespace rezident\attachfile\views\extra\image;


class ImageResizerStretch extends ImageResizer
{
    /**
     * @inheritdoc
     */
    public function getResizedImage($image)
    {
        $destinationImage = $this->createImage($this->width, $this->height);

        imagecopyresampled(
            $destinationImage,
            $image,
            0,
            0,
            0,
            0,
            $this->width,
            $this->height,
            imagesx($image),
            imagesy($image)
        );

        return $destinationImage;
    }

}