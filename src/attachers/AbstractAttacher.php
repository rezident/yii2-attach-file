<?php


namespace rezident\attachfile\attachers;


use rezident\attachfile\behaviors\AttachFileBehavior;
use rezident\attachfile\exceptions\FileNotFoundException;
use rezident\attachfile\extensions\DateTime;
use rezident\attachfile\models\AttachedFile;

abstract class AbstractAttacher
{

    /**
     * @var AttachFileBehavior
     */
    private $behaviour;

    public function __construct(AttachFileBehavior $behavior)
    {
        $this->behaviour = $behavior;
    }

    /**
     * @param string $absolutePath
     * @param bool $isMain
     * @param string $name
     *
     * @return AttachedFile
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function attachFile($absolutePath, $isMain, $name)
    {
        $this->behaviour->checkIsSaved();

        if ($isMain) {
            $this->behaviour->getAttachedFilesCollection()->resetIsMainInAllFiles(false);
        }

        $file = new AttachedFile();
        $file->model_key = $this->behaviour->getModelKey();
        $file->model_id = $this->behaviour->owner->primaryKey;
        $file->name = $name;
        $file->md5_hash = md5_file($absolutePath);
        $file->size = filesize($absolutePath);
        $file->is_main = $isMain;
        $file->uploaded_at = (new DateTime())->getMysqlDateTimeString();

        $file->storeOriginalFile($absolutePath);
        $this->behaviour->getAttachedFilesCollection()->add($file);
        $this->behaviour->getAttachedFilesCollection()->save();

        return $file;
    }

    /**
     * Attaches the file
     *
     * @param mixed $source Source of the file for attaching
     * @param bool $isMain Whether the file is main
     * @param string|null $fileName Name of the file
     *
     * @return AttachedFile
     * @throws FileNotFoundException
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function attach($source, $isMain = false, $fileName = null)
    {
        $absolutePath = $this->getAbsolutePath($source);

        if (file_exists($absolutePath) == false) {
            throw new FileNotFoundException($absolutePath);
        }

        if ($fileName === null) {
            $fileName = basename($absolutePath);
        }

        return $this->attachFile($absolutePath, $isMain, $fileName);

    }

    /**
     * Returns the absolute path of file for attaching
     *
     * @param mixed $source
     * @return string
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    abstract protected function getAbsolutePath($source);
}