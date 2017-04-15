<?php


namespace rezident\attachfile\behaviors;


use ReflectionClass;
use rezident\attachfile\attachers\AbstractAttacher;
use rezident\attachfile\attachers\UploadedFileAttacher;
use rezident\attachfile\collections\AttachedFilesCollection;
use rezident\attachfile\exceptions\ModelIsUnsaved;
use rezident\attachfile\models\AttachedFile;
use rezident\attachfile\models\AttachedFileView;
use yii\base\Behavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class AttachFileBehavior
 * @author Yuri Nazarenko / rezident <mail@rezident.org>
 *
 * @property AttachedFile[] $attachedFiles
 */
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
    private $attachedFilesCollection;

    /**
     * @inheritdoc
     */
    public function init()
    {
        $this->attachedFilesCollection = new AttachedFilesCollection($this);
        parent::init();
    }

    /**
     * Checks whether the model is saved
     *
     * @throws ModelIsUnsaved
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
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
        if (isset($this->modelKey) == false) {
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
    public function getAttachedFilesCollection()
    {
        return $this->attachedFilesCollection;
    }

    /**
     * @param string $className
     *
     * @return AbstractAttacher
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getAttacher($className = UploadedFileAttacher::class)
    {
        return new $className($this);
    }

    /**
     * @return ActiveQuery
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getAttachedFiles()
    {
        return $this->owner->hasMany(
            AttachedFile::class,
            ['model_id' => key($this->owner->getPrimaryKey(true))]
        )->andOnCondition([AttachedFile::tableName() . '.model_key' => $this->getModelKey()]);
    }

    public function getAttachedMainFileView()
    {
        return $this->owner->hasOne(AttachedFileView::class, ['attached_file_id' => 'id'])
            ->viaTable(AttachedFile::tableName(), ['model_id' => key($this->owner->getPrimaryKey(true))])
            ->andOnCondition([AttachedFile::tableName() . '.model_key' => $this->getModelKey()])
            ->andOnCondition([AttachedFile::tableName() . '.is_main' => 1]);
    }

}