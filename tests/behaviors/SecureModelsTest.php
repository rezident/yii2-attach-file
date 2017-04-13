<?php


namespace rezident\attachfile\tests\behaviors;


use PHPUnit\Framework\TestCase;
use rezident\attachfile\attachers\LocalFileAttacher;
use rezident\attachfile\models\AttachedFile;
use rezident\attachfile\models\AttachedFileView;
use rezident\attachfile\tests\SyntheticModel;
use yii\helpers\FileHelper;

class SecureModelsTest extends TestCase
{
    /**
     * @var SyntheticModel
     */
    private $syntheticModel;

    public function setUp()
    {
        $this->syntheticModel = new SyntheticModel();
        $this->syntheticModel->save();
        AttachedFile::getDb()->createCommand('SET FOREIGN_KEY_CHECKS = 0')->execute();
        AttachedFile::getDb()->createCommand('TRUNCATE ' . AttachedFile::tableName())->execute();
        AttachedFile::getDb()->createCommand('TRUNCATE ' . AttachedFileView::tableName())->execute();
        FileHelper::removeDirectory(\Yii::getAlias('@app/files'));
    }

    /**
     * @test
     */
    public function doNotChangeAttachFileAttributes()
    {
        $file = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../bootstrap.php');
        $oldId = $file->id;
        $oldModelKey = $file->model_key;
        $oldModelId = $file->model_id;
        $oldName = $file->name;
        $oldMd5Hash = $file->md5_hash;
        $oldSize = $file->size;
        $oldUploadedAt = $file->uploaded_at;

        $file->id = 'zzz';
        $file->model_key = 'zzz';
        $file->model_id = 'zzz';
        $file->name = 'zzz';
        $file->md5_hash = 'zzz';
        $file->size = 'zzz';
        $file->uploaded_at = 'zzz';
        $file->save();

        $this->assertEquals($oldId, $file->id);
        $this->assertEquals($oldModelKey, $file->model_key);
        $this->assertEquals($oldModelId, $file->model_id);
        $this->assertEquals($oldName, $file->name);
        $this->assertEquals($oldMd5Hash, $file->md5_hash);
        $this->assertEquals($oldSize, $file->size);
        $this->assertEquals($oldUploadedAt, $file->uploaded_at);
    }
}