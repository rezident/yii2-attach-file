<?php


namespace rezident\attachfile\views\extra\image;


class ImageResizerPad extends ImageResizer
{
    /**
     * @inheritdoc
     */
    public function getResizedImage($image)
    {
        $factor = $this->getMaxFactor($image);

        $destinationImage = $this->createImage($this->width, $this->height);
        $sourceWidth = imagesx($image);
        $sourceHeight = imagesy($image);
        $destinationWidth = $sourceWidth / $factor;
        $destinationHeight = $sourceHeight / $factor;

        $destinationX = ($destinationWidth < $this->width) ? (($this->width - $destinationWidth) / 2) : 0;
        $destinationY = ($destinationHeight < $this->height) ? (($this->height - $destinationHeight) / 2) : 0;

        imagecopyresampled(
            $destinationImage,
            $image,
            $destinationX,
            $destinationY,
            0,
            0,
            $destinationWidth,
            $destinationHeight,
            $sourceWidth,
            $sourceHeight
        );

        if ($this->padsExist($destinationX, $destinationY)) {
            $this->paintPads($destinationImage, $destinationWidth, $destinationHeight, $destinationX, $destinationY);
        }

        return $destinationImage;

    }

    /**
     * @param resource $image
     *
     * @return float
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function getMaxFactor($image)
    {
        return max($this->getImageFactors($image));
    }

    /**
     * @param float $x
     * @param float $y
     *
     * @return bool
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function padsExist($x, $y)
    {
        return $x > 0 || $y > 0;
    }

    /**
     * @return string
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function getSanitizedPadColor()
    {
        return (preg_match('/^[0-F]{6}$/', $this->padColor) == 0) ? '000000' : $this->padColor;
    }

    /**
     * @param resource $destination
     * @param float $width
     * @param float $height
     * @param float $destinationX
     * @param float $destinationY
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function paintPads($destination, $width, $height, $destinationX, $destinationY)
    {
        $padColorString = $this->getSanitizedPadColor();
        $padColor = $this->getColorAllocate($destination, $padColorString);

        if ($destinationX > 0) {
            imagefilledrectangle($destination, 0, 0, $destinationX - 1, $height, $padColor);
            imagefilledrectangle($destination, $destinationX + $width, 0, $destinationX * 2 + $width - 1, $height, $padColor);
        }

        if ($destinationY > 0) {
            imagefilledrectangle($destination, 0, 0, $width, $destinationY - 1, $padColor);
            imagefilledrectangle($destination, 0, $destinationY + $height, $width, $destinationY * 2 + $height - 1, $padColor);
        }
    }

    /**
     * @param resource $destination
     * @param string $padColor
     *
     * @return int
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function getColorAllocate($destination, $padColor)
    {
        $colorBin = hex2bin($padColor);
        $red = ord($colorBin[0]);
        $green = ord($colorBin[1]);
        $blue = ord($colorBin[2]);
        return imagecolorallocate($destination, $red, $green, $blue);
    }
}