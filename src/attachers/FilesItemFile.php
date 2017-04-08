<?php


namespace rezident\attachfile\attachers;


/**
 * Class FilesItemFile
 * @author Yuri Nazarenko / rezident <mail@rezident.org>
 *
 * Attacher for an item of $_FILES variable
 */
class FilesItemFile extends AbstractAttacher
{
    /**
     * @inheritdoc
     */
    protected function getAbsolutePath($source)
    {
        return $source['tmp_name'];
    }

}