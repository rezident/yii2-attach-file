<?php

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
        $this->createTable('{{%attached_file}}', [
            'id' => $this->primaryKey(),
            'model_key' => $this->string(64)->notNull(),
            'model_id' => $this->integer()->notNull(),
            'name' => $this->string(256)->notNull(),
            'md5_hash' => $this->string(32)->notNull(),
            'size' => $this->integer()->notNull(),
            'position' => $this->integer()->notNull(),
            'is_main' => $this->boolean()->notNull()->defaultValue(false),
            'uploaded_at' => $this->dateTime()->notNull()
        ]);

        $this->createIndex('idx_model', '{{%attached_file}}', ['model_key', 'model_id']);
        $this->createIndex('idx_md5_hash', '{{%attached_file}}', 'md5_hash');
        $this->createIndex('idx_position', '{{%attached_file}}', 'position');
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable('{{%attached_file}}');
    }
}
