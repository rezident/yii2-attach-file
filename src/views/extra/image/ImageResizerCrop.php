<?php


namespace rezident\attachfile\views\extra\image;


class ImageResizerCrop extends ImageResizer
{
    /**
     * @inheritdoc
     */
    public function getResizedImage($image)
    {
        $destinationImage = $this->createImage($this->width, $this->height);
        $factor = $this->getMinFactor($image);

        $sourceWidth = imagesx($image);
        $sourceHeight = imagesy($image);

        imagecopyresampled(
            $destinationImage,
            $image,
            0,
            0,
            $this->getSourceX($sourceWidth, $factor),
            $this->getSourceY($sourceHeight, $factor),
            $sourceWidth / $factor,
            $sourceHeight / $factor,
            $sourceWidth,
            $sourceHeight
        );

        return $destinationImage;
    }

    /**
     * @param resource $image
     * @return float
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function getMinFactor($image)
    {
        return min($this->getImageFactors($image));
    }

    /**
     * @return bool
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function horizontalIsCenter()
    {
        return $this->anchor == self::ANCHOR_CENTER_TOP || $this->anchor == self::ANCHOR_CENTER_CENTER || $this->anchor == self::ANCHOR_CENTER_BOTTOM;
    }

    /**
     * @return bool
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function horizontalIsRight()
    {
        return $this->anchor == self::ANCHOR_RIGHT_TOP || $this->anchor == self::ANCHOR_RIGHT_CENTER || $this->anchor == self::ANCHOR_RIGHT_BOTTOM;
    }

    /**
     * @return bool
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function verticalIsCenter()
    {
        return $this->anchor == self::ANCHOR_LEFT_CENTER || $this->anchor == self::ANCHOR_CENTER_CENTER || $this->anchor == self::ANCHOR_RIGHT_CENTER;
    }

    /**
     * @return bool
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function verticalIsBottom()
    {
        return $this->anchor == self::ANCHOR_LEFT_BOTTOM || $this->anchor == self::ANCHOR_CENTER_BOTTOM || $this->anchor == self::ANCHOR_RIGHT_BOTTOM;
    }

    /**
     * @param float $sourceWidth
     * @param float $factor
     *
     * @return float
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function getSourceX($sourceWidth, $factor)
    {
        $sourceX = 0;
        $destinationWidth = $this->width * $factor;
        if ($sourceWidth > $destinationWidth) {
            if ($this->horizontalIsCenter()) {
                $sourceX = ($sourceWidth - $destinationWidth) / 2;
            }

            if ($this->horizontalIsRight()) {
                $sourceX = $sourceWidth - $destinationWidth;
            }

        }

        return $sourceX;
    }

    /**
     * @param float $sourceHeight
     * @param float $factor
     *
     * @return float
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function getSourceY($sourceHeight, $factor)
    {
        $sourceY = 0;
        $destinationHeight = $this->height * $factor;
        if ($sourceHeight > $destinationHeight) {
            if ($this->verticalIsCenter()) {
                $sourceY = ($sourceHeight - $destinationHeight) / 2;
            }

            if ($this->verticalIsBottom()) {
                $sourceY = $sourceHeight - $destinationHeight;
            }

        }

        return $sourceY;
    }

}