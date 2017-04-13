<?php


namespace rezident\attachfile\models;


use DateTime;
use rezident\attachfile\views\AbstractView;
use yii\db\ActiveRecord;
use yii\helpers\Json;

/**
 * Class AttachedFileView
 * @author Yuri Nazarenko / rezident <mail@rezident.org>
 *
 * @property integer $id
 * @property integer $attached_file_id
 * @property string $view_config
 * @property string $view_config_hash
 * @property string $extension
 * @property DateTime $created_at
 *
 * @property AttachedFile $attachedFile
 */
class AttachedFileView extends ActiveRecord
{

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttachedFile()
    {
        return $this->hasOne(AttachedFile::class, ['id' => 'attached_file_id']);
    }

    public function setAttachedFile(AttachedFile $attachedFile)
    {
        $this->attachedFile = $attachedFile;
    }

    /**
     * @inheritdoc
     */
    public function delete()
    {
        $view = $this->getView();
        unlink($view->getViewFilePath($this));
        if($view->isViewPathEmpty()) {
            $view->deleteViewPath();
        }

        return parent::delete();
    }

    /**
     * Returns the view
     *
     * @return AbstractView
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getView()
    {
        $config = Json::decode($this->view_config);
        $config['attachedFile'] = $this->attachedFile;
        /** @var AbstractView $view */
        $view = \Yii::createObject($config);
        return $view;
    }


    /**
     * Returns a query
     *
     * @return AttachedFileViewQuery
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    static public function find()
    {
        return new AttachedFileViewQuery(get_called_class());
    }
}