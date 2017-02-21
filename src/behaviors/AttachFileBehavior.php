<?php


namespace rezident\attachfile\behaviors;


use DateTime;
use ReflectionClass;
use rezident\attachfile\attachers\LocalFile;
use rezident\attachfile\collections\AttachedFilesCollection;
use rezident\attachfile\exceptions\ModelIsUnsaved;
use rezident\attachfile\models\AttachedFile;
use rezident\attachfile\traits\AttachedFileImportTrait;
use yii\base\Behavior;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;

class AttachFileBehavior extends Behavior
{

    /**
     * @var ActiveRecord
     */
    public $owner;

    /**
     * @var string
     */
    private $modelKey;

    /**
     * @var AttachedFilesCollection
     */
    private $attachedFiles;


    public function init()
    {
        $this->attachedFiles = new AttachedFilesCollection($this);
        parent::init();
    }

    public function events()
    {
        return [

        ];
    }

    public function checkIsSaved()
    {
        if (isset($this->owner->primaryKey) == false) {
            throw new ModelIsUnsaved();
        }
    }

    /**
     * Sets the model key
     *
     * @param string $modelKey
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function setModelKey($modelKey)
    {
        $this->modelKey = $modelKey;
    }

    /**
     * Returns the model key
     *
     * @return string
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getModelKey()
    {
        if(isset($this->modelKey) == false) {

            $reflectionClass = new ReflectionClass($this->owner);
            $this->modelKey = $reflectionClass->getShortName();
        }

        return $this->modelKey;
    }

    /**
     * Returns the attached files for model
     *
     * @return AttachedFilesCollection
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getAttachedFiles()
    {
        return $this->attachedFiles;
    }

    /**
     * @param string $className
     * @return LocalFile
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getAttacher($className = LocalFile::class)
    {
        return new $className($this);
    }

}