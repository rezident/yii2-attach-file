<?php

use rezident\attachfile\models\AttachedFile;
use rezident\attachfile\models\AttachedFileView;
use yii\db\Migration;

/**
 * Handles the creation of table `attach_file_view`.
 */
class m170206_112211_create_attached_file_view_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->createTable(AttachedFileView::tableName(), [
            'id' => $this->primaryKey(),
            'attached_file_id' => $this->integer()->notNull(),
            'view_config' => $this->text()->notNull(),
            'view_config_hash' => $this->string(8)->notNull(),
            'extension' => $this->string(8)->notNull(),
            'created_at' => $this->dateTime()->notNull()
        ]);

        $this->addForeignKey('attached_file_view_attached_file', AttachedFileView::tableName(), 'attached_file_id', AttachedFile::tableName(), 'id');
        $this->createIndex('idx_attached_filed_id_view_params_hash', AttachedFileView::tableName(), ['attached_file_id', 'view_config_hash'], true);
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropTable(AttachedFileView::tableName());
    }
}
