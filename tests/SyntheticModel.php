<?php


namespace rezident\attachfile\tests;


use rezident\attachfile\behaviors\AttachFileBehavior;
use rezident\attachfile\traits\AttachFileBehaviorDocTrait;
use yii\db\ActiveRecord;

class SyntheticModel extends ActiveRecord
{
    use AttachFileBehaviorDocTrait;

    public $id;

    public $primaryKey;

    static public function tableName()
    {
        return '{{%attached_file}}';
    }

    public function rules()
    {
        return [
            [['id'], 'safe']
        ];
    }

    public function behaviors()
    {
        return [
            'specified' => [
                'class' => AttachFileBehavior::class,
                'modelKey' => 'specified'
            ],
            'notSpecified' => AttachFileBehavior::class
        ];
    }

    public function save($runValidation = true, $attributeNames = null)
    {
        if (!$this->beforeSave(true)) {
            return false;
        }

        $this->isNewRecord = false;
        $this->id = 5;
        $this->primaryKey = 5;
        return true;
    }
}