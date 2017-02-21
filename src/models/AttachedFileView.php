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
 * @property string $settings
 * @property DateTime $created_at
 *
 * @property AttachedFile $attachedFile
 */
class AttachedFileView extends ActiveRecord
{

    public function getWebPath()
    {
        $webPath = \Yii::getAlias(AttachFileModule::getInstance()->webPath);
        if ($webPath == false) {
            throw new InvalidConfigException('You should set «webPath» in config');
        }

        return $webPath;
    }

    public function getViewsPath()
    {
        $viewsPath = \Yii::getAlias(AttachFileModule::getInstance()->viewsPath);
        if ($viewsPath == false) {
            throw new InvalidConfigException('You should set «viewsPath» in config');
        }

        return $viewsPath;
    }

    /**
     * @param AttachedFile|null $attachedFile
     *
     * @return string
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getUrl($attachedFile = null, $extension = null)
    {
        return $this->getWebPath() . '/' . $this->getRelativePath($attachedFile, $extension);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getAttachedFile()
    {
        return $this->hasOne(AttachedFile::class, ['id' => 'attached_file_id']);
    }

    public function saveViewContent($content, $attachedFile = null, $extension = null)
    {
        $viewPath = $this->getViewsPath() . '/' . $this->getRelativePath($attachedFile, $extension);
        $dirName = pathinfo($viewPath, PATHINFO_DIRNAME);
        if(FileHelper::createDirectory($dirName));
        file_put_contents($viewPath, $content);
    }

    private function getRelativePath($attachedFile = null, $extension = null)
    {
        $attachedFile = ($attachedFile) ?: $this->attachedFile;
        $extension = ($extension) ?: $attachedFile->getExtension();
        return $attachedFile->md5_hash . '/' . $this->settings . '.' . $extension;
    }

    static public function find()
    {
        return new AttachedFileViewQuery(get_called_class());
    }
}