<?php


namespace rezident\attachfile\views;


class PngView extends ImageView
{
    /**
     * @inheritdoc
     */
    protected function getExtension()
    {
        return 'png';
    }

    /**
     * @inheritdoc
     */
    protected function output($image)
    {
        imagepng($image, null, $this->quality / 100 * 9);
    }

}