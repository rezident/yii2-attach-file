<?php

use rezident\attachfile\models\AttachedFile;
use yii\db\Migration;

/**
 * Handles the creation of table `attach_file`.
 */
class m161221_074034_create_attach_file_table extends Migration
{

    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable(AttachedFile::tableName(), [
            'id' => $this->primaryKey()->unsigned(),
            'model_key' => $this->string(64)->notNull(),
            'model_id' => $this->integer()->unsigned()->notNull(),
            'name' => $this->string(256)->notNull(),
            'md5_hash' => $this->string(32)->notNull(),
            'size' => $this->integer()->unsigned()->notNull(),
            'position' => $this->integer()->unsigned()->notNull(),
            'is_main' => $this->boolean()->notNull()->defaultValue(false),
            'uploaded_at' => $this->dateTime()->notNull()
        ]);

        $this->createIndex('idx_model', AttachedFile::tableName(), ['model_key', 'model_id']);
        $this->createIndex('idx_md5_hash', AttachedFile::tableName(), 'md5_hash');
        $this->createIndex('idx_position', AttachedFile::tableName(), 'position');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(AttachedFile::tableName());
    }
}
