<?php


namespace rezident\attachfile\attachers;


/**
 * Class FilesItemFile
 * @author Yuri Nazarenko / rezident <mail@rezident.org>
 *
 * Attacher for an item of $_FILES variable
 */
class UploadedFileAttacher extends AbstractAttacher
{
    /**
     * @inheritdoc
     */
    protected function getAbsolutePath($source)
    {
        /** @var \yii\web\UploadedFile $source */
        return $source->tempName;
    }

}