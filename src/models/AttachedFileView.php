<?php


namespace rezident\attachfile\models;


use DateTime;
use rezident\attachfile\AttachFileModule;
use yii\base\InvalidConfigException;
use yii\db\ActiveRecord;
use yii\helpers\FileHelper;

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