<?php


namespace rezident\attachfile\views;


class JpgView extends ImageView
{
    /**
     * @inheritdoc
     */
    protected function getExtension()
    {
        return 'jpg';
    }

    /**
     * @inheritdoc
     */
    protected function output($image)
    {
        imagejpeg($image, null, $this->quality);
    }
}