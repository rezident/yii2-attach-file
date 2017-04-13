<?php


namespace rezident\attachfile\traits;


use rezident\attachfile\attachers\AbstractAttacher;
use rezident\attachfile\attachers\UploadedFileAttacher;
use rezident\attachfile\collections\AttachedFilesCollection;

/**
 * Class AttachFileBehaviorDocTrait
 * @author Yuri Nazarenko / rezident <mail@rezident.org>
 *
 * @method AttachedFilesCollection getAttachedFilesCollection Returns the attached files collection for model
 * @method AbstractAttacher getAttacher($className = UploadedFileAttacher::class) Returns an attacher
 */
trait AttachFileBehaviorDocTrait
{

}