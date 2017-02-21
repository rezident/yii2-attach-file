<?php


namespace rezident\attachfile\collections;


use rezident\attachfile\behaviors\AttachFileBehavior;
use rezident\attachfile\models\AttachedFile;

class AttachedFilesCollection
{
    /**
     * @var AttachedFile[]
     */
    public $attachedFiles;

    /**
     * @var AttachFileBehavior
     */
    private $behavior;

    public function __construct(AttachFileBehavior $behavior)
    {
        $this->behavior = $behavior;
    }

    private function initialize()
    {
        if(isset($this->attachedFiles) == false) {
            $this->fetchAttachedFiles();
        }
    }

    private function fetchAttachedFiles()
    {
        $this->attachedFiles = AttachedFile::find()->byModelKeyAndModelId($this->behavior->getModelKey(), $this->behavior->owner->primaryKey)->all();
    }

    public function resetIsMainInAllFiles()
    {
        $this->initialize();
        foreach ($this->attachedFiles as $attachedFile) {
            $attachedFile->is_main = false;
        }
    }

    /**
     * Adds attached file to the model
     *
     * @param AttachedFile $file
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function addFile(AttachedFile $file)
    {
        $this->initialize();
        $this->attachedFiles[] = $file;
    }

    public function saveFiles()
    {
        $this->initialize();
        $isMainExists = false;
        foreach ($this->attachedFiles as $position => $attachedFile) {
            $attachedFile->position = $position + 1;
            if($attachedFile->is_main) {
                if($isMainExists) {
                    $attachedFile->is_main = false;
                } else {
                    $isMainExists = true;
                }
            }

            $attachedFile->save();
        }
    }

    public function count()
    {
        $this->initialize();
        return count($this->attachedFiles);
    }
}