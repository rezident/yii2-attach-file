<?php


namespace rezident\attachfile\behaviors;


use yii\base\Behavior;
use yii\db\ActiveRecord;

class AttributesReadOnlyBehavior extends Behavior
{
    public $attributes = [];

    /**
     * @var ActiveRecord
     */
    public $owner;

    public function events()
    {
        return [
            ActiveRecord::EVENT_BEFORE_UPDATE => function () {
                foreach ($this->attributes as $attribute) {
                    $this->owner->$attribute = $this->owner->oldAttributes[$attribute];
                }

            }

        ];
    }
}
