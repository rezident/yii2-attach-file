<?php


namespace rezident\attachfile\models;


use yii\db\ActiveQuery;

/**
 * Class AttachedFileViewQuery
 * @author Yuri Nazarenko / rezident <mail@rezident.org>
 *
 * @method AttachedFileView[] all($db = null)
 * @method AttachedFileView one($db = null)
 */
class AttachedFileViewQuery extends ActiveQuery
{
    /**
     * @param AttachedFile $attachedFile
     * @return $this
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function byAttachedFile(AttachedFile $attachedFile)
    {
        return $this->andWhere([
            'attached_file_id' => $attachedFile->id
        ]);
    }

    /**
     * @param string $settings
     * @return $this
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function bySettings($settings)
    {
        return $this->andWhere([
            'settings' => $settings
        ]);
    }

    /**
     * @param string $md5Hash
     * @return $this
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function byAttachedFileMd5Hash($md5Hash)
    {
        return $this->joinWith('attachedFile')->andWhere([
            'md5_hash' => $md5Hash
        ]);
    }
}