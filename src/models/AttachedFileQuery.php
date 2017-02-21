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
    public function byModelKeyAndModelId($modelKey, $modelId)
    {
        return $this->andWhere([
            'model_key' => $modelKey,
            'model_id' => $modelId
        ])->orderBy(['position' => SORT_ASC]);
    }
}