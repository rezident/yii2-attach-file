<?php


namespace rezident\attachfile\models;


use yii\db\ActiveQuery;

/**
 * Class AttachedFileQuery
 * @author Yuri Nazarenko / rezident <mail@rezident.org>
 *
 * @method AttachedFile[] all($db = null)
 * @method AttachedFile one($db = null)
 */
class AttachedFileQuery extends ActiveQuery
{
    /**
     * Adds the condition for searching by modelKey and modelId
     *
     * @param string $modelKey
     * @param int $modelId
     *
     * @return $this
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function byModelKeyAndModelId($modelKey, $modelId)
    {
        return $this->andWhere([
            'model_key' => $modelKey,
            'model_id' => $modelId
        ])->orderBy(['position' => SORT_ASC]);
    }

    /**
     * Adds the condition for searching by md5_hash
     *
     * @param string $md5Hash
     *
     * @return $this
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function byMd5Hash($md5Hash)
    {
        return $this->andWhere([
            'md5_hash' => $md5Hash
        ]);
    }
}