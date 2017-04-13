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
     * Adds the condition for searching by attached file
     *
     * @param AttachedFile $attachedFile
     *
     * @return $this
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function byAttachedFile(AttachedFile $attachedFile)
    {
        return $this->andWhere([
            'attached_file_id' => $attachedFile->id
        ]);
    }

    /**
     * Adds the condition for searching by viewConfigHash
     *
     * @param string $viewConfigHash
     *
     * @return $this
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function byViewConfigHash($viewConfigHash)
    {
        return $this->andWhere([
            'view_config_hash' => $viewConfigHash
        ]);
    }

    /**
     * Adds the condition for searching by model id
     *
     * @param int $id
     *
     * @return $this
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function byId($id)
    {
        return $this->andWhere([
            AttachedFileView::tableName() . '.id' => $id
        ]);
    }
}