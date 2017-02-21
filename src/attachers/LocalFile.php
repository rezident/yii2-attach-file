<?php


namespace rezident\attachfile\attachers;


class LocalFile extends AbstractAttacher
{
    /**
     * @inheritdoc
     */
    protected function getAbsolutePath($source)
    {
        return $source;
    }


}