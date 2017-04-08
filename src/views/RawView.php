<?php


namespace rezident\attachfile\views;


class RawView extends AbstractView
{
    /**
     * @inheritdoc
     */
    public function render()
    {
        echo file_get_contents($this->attachedFile->getOriginalFilePath());
    }
}