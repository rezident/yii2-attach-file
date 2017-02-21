<?php

namespace rezident\attachfile\tests\behaviors;
require_once __DIR__ . '/../bootstrap.php';


use PHPUnit\Framework\TestCase;
use rezident\attachfile\behaviors\AttachFileBehavior;
use rezident\attachfile\models\AttachedFile;
use rezident\attachfile\models\AttachedFileView;
use rezident\attachfile\tests\SyntheticModel;
use yii\helpers\FileHelper;


class AttachFileBehaviorTest extends TestCase
{

    /**
     * @var SyntheticModel
     */
    private $syntheticModel;

    public function setUp()
    {
        $this->syntheticModel = new SyntheticModel();
        AttachedFile::getDb()->createCommand('SET FOREIGN_KEY_CHECKS = 0')->execute();
        AttachedFile::getDb()->createCommand('TRUNCATE ' . AttachedFile::tableName())->execute();
        AttachedFile::getDb()->createCommand('TRUNCATE ' . AttachedFileView::tableName())->execute();
        FileHelper::removeDirectory(\Yii::getAlias('@app/files'));
    }

    /**
     * @test
     */
    public function returnSpecifiedModelKey()
    {
        /** @var AttachFileBehavior $behaviour */
        $behaviour = $this->syntheticModel->behaviors['specified'];
        $this->assertEquals('specified', $behaviour->getModelKey());
    }

    /**
     * @test
     */
    public function returnShortNameAsModelKey()
    {
        /** @var AttachFileBehavior $behaviour */
        $behaviour = $this->syntheticModel->behaviors['notSpecified'];
        $this->assertEquals('SyntheticModel', $behaviour->getModelKey());
    }

    /**
     * @test
     */
    public function returnEmptyArrayAsAttachedFiles()
    {
        $this->assertEquals(0, $this->syntheticModel->getAttachedFiles()->count());
    }

    /**
     * @expectedException \rezident\attachfile\exceptions\ModelIsUnsaved
     * @test
     */
    public function attachFileToUnsavedModel()
    {
        $this->syntheticModel->getAttacher()->attach(__FILE__);
    }


    /**
     * @test
     */
    public function attachLocalFile()
    {
        $this->syntheticModel->save();
        $attachedFile = $this->syntheticModel->getAttacher()->attach(__FILE__);
        $this->assertFileEquals(__FILE__, __DIR__ . '/../../src/files/originals/specified/' . $attachedFile->md5_hash);
    }
}
