<?php


namespace rezident\attachfile\models;


use DateTime;
use rezident\attachfile\AttachFileModule;
use rezident\attachfile\behaviors\AttributesReadOnlyBehavior;
use rezident\attachfile\views\ViewsFactory;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;

/**
 * Class AttachedFile
 * @author Yuri Nazarenko / rezident <mail@rezident.org>
 *
 * @property int $id
 * @property string $model_key
 * @property int $model_id
 * @property string $name
 * @property string $md5_hash
 * @property int $size
 * @property int $position
 * @property bool $is_main
 * @property DateTime $uploaded_at
 */
class AttachedFile extends ActiveRecord
{
    /**
     * @var ViewsFactory
     */
    private $viewsFactory;

    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'readOnly' => [
                'class' => AttributesReadOnlyBehavior::class,
                'attributes' => [
                    'id', 'model_key', 'model_id', 'name', 'md5_hash', 'size', 'uploaded_at'
                ]
            ]
        ];
    }

    /**
     * Stores the original file in storage
     *
     * @param string $absolutePath
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function storeOriginalFile($absolutePath)
    {
        $originalsPath = $this->getOriginalsPath();
        FileHelper::createDirectory($originalsPath);
        $originalFilePath = $this->getOriginalFilePath();
        if (is_file($originalFilePath) == false) {
            copy($absolutePath, $originalFilePath);
        }
    }

    /**
     * Returns the view factory
     *
     * @return ViewsFactory
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getView()
    {
        if (isset($this->viewsFactory) == false) {
            $this->viewsFactory = new ViewsFactory($this);
        }

        return $this->viewsFactory;
    }


    /**
     * Returns path of originals
     *
     * @return string
     *
     * @throws InvalidConfigException
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getOriginalsPath()
    {
        $originalsPath = \Yii::getAlias(AttachFileModule::getInstance()->originalsPath);
        if($originalsPath == false) {
            throw new InvalidConfigException('You should set «originalsPath» in config');
        }

        return $originalsPath . '/' . $this->model_key;
    }

    /**
     * Returns path to the original file
     *
     * @return string
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getOriginalFilePath()
    {
        return $this->getOriginalsPath() . '/' . $this->md5_hash;
    }

    /**
     * Returns MIME-type of the file
     *
     * @return string
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getMimeType()
    {
        return mime_content_type($this->getOriginalFilePath());
    }

    /**
     * Returns an extension of the file
     *
     * @return string
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getExtension()
    {
        return pathinfo($this->name, PATHINFO_EXTENSION);
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $attachedFileViews = AttachedFileView::find()->byAttachedFile($this)->joinWith('attachedFile')->all();
        foreach ($attachedFileViews as $attachedFileView) {
            $attachedFileView->delete();
        }


        $deleteResult = parent::delete();
        if(self::find()->byMd5Hash($this->md5_hash)->count() == 0) {
            unlink($this->getOriginalFilePath());
        }

        return $deleteResult;
    }

    /**
     * Returns a query
     *
     * @return AttachedFileQuery
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    static public function find()
    {
        return new AttachedFileQuery(get_called_class());
    }
}