<?php


namespace rezident\attachfile\attachers;

/**
 * Class LocalFile
 * @author Yuri Nazarenko / rezident <mail@rezident.org>
 *
 * Attacher for local files
 */
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