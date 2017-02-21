<?php


namespace rezident\attachfile\traits;


use rezident\attachfile\attachers\LocalFile;
use rezident\attachfile\collections\AttachedFilesCollection;
use rezident\attachfile\models\AttachedFile;

/**
 * Class AttachFileBehaviorDocTrait
 * @author Yuri Nazarenko / rezident <mail@rezident.org>
 *
 * @method AttachedFilesCollection getAttachedFiles Returns the attached files for model
 * @method AttachedFile attachLocalFile($absolutePath, $isMain = false, $name = null)
 * @method LocalFile getAttacher($className = LocalFile::class)
 */
trait AttachFileBehaviorDocTrait
{

}