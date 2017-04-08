<?php


namespace rezident\attachfile\views\extra\image;


class ImageResizerProportional extends ImageResizer
{

    /**
     * @inheritdoc
     */
    public function getResizedImage($image)
    {
        $sourceImageWidth = imagesx($image);
        $sourceImageHeight = imagesy($image);

        $factor = $this->getFactor($sourceImageWidth, $sourceImageHeight);
        $destinationWidth = $sourceImageWidth / $factor;
        $destinationHeight = $sourceImageHeight / $factor;
        $destinationImage = $this->createImage($destinationWidth, $destinationHeight);

        imagecopyresampled(
            $destinationImage,
            $image,
            0,
            0,
            0,
            0,
            $destinationWidth,
            $destinationHeight,
            $sourceImageWidth,
            $sourceImageHeight
        );

        return $destinationImage;
    }

    /**
     * @param int|null $width
     * @param int|null $height
     *
     * @return float
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function getFactor($width, $height)
    {
        return ($this->width) ? $width / $this->width : $height / $this->height;
    }

}