<?php

namespace rezident\attachfile\tests\behaviors;
require_once __DIR__ . '/../bootstrap.php';


use Generator;
use PHPUnit\Framework\TestCase;
use rezident\attachfile\attachers\LocalFileAttacher;
use rezident\attachfile\behaviors\AttachFileBehavior;
use rezident\attachfile\models\AttachedFile;
use rezident\attachfile\models\AttachedFileView;
use rezident\attachfile\tests\SyntheticModel;
use yii\helpers\FileHelper;


class AttachFileTest extends TestCase
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
        $this->assertEquals('synthetic', $behaviour->getModelKey());
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
        $this->assertEquals(0, $this->syntheticModel->getAttachedFilesCollection()->count());
    }

    /**
     * @expectedException \rezident\attachfile\exceptions\ModelIsUnsaved
     * @test
     */
    public function attachFileToUnsavedModel()
    {
        $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__FILE__);
    }


    /**
     * @test
     */
    public function attachLocalFile()
    {
        $this->syntheticModel->save();
        $attachedFile = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__FILE__);
        $this->assertFileEquals(__FILE__, __DIR__ . '/../../src/files/originals/synthetic/' . $attachedFile->md5_hash);
    }

    /**
     * @test
     */
    public function attachLocalFilesWithRightPositions()
    {
        $this->syntheticModel->save();
        $attachedFile = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__FILE__);
        $attachedFile1 = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../img/logo.png');
        $this->assertEquals($attachedFile->position, 1);
        $this->assertEquals($attachedFile1->position, 2);
        $this->assertEquals(2, $this->syntheticModel->getAttachedFilesCollection()->count());
    }

    /**
     * @test
     */
    public function moveAttachedFiles()
    {
        $this->syntheticModel->save();
        $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__FILE__);
        $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../img/logo.png');
        $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../bootstrap.php');
        $attachedFiles = $this->syntheticModel->getAttachedFilesCollection();
        $attachedFiles->move(2, 0);
        $this->assertEquals('bootstrap.php', $attachedFiles->get(0)->name);
        $attachedFiles->move(1, 2);
        $this->assertEquals('logo.png', $attachedFiles->get(2)->name);
        $attachedFiles->move(0, 1);
        $this->assertEquals(basename(__FILE__), $attachedFiles->get(1)->name);
    }

    /**
     * @test
     */
    public function setAsMainFile()
    {
        $this->syntheticModel->save();
        $file1 = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__FILE__, true);
        $this->assertTrue($file1->is_main);
        $file2 = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../img/logo.png', true);
        $this->assertTrue($file2->is_main);
        $this->assertFalse($file1->is_main);
        $file3 = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../bootstrap.php', true);
        $this->assertTrue($file3->is_main);
        $this->assertFalse($file1->is_main);
        $this->assertFalse($file2->is_main);

        $this->assertEquals($file3, $this->syntheticModel->getAttachedFilesCollection()->getMain());
        $this->assertTrue($this->syntheticModel->getAttachedFilesCollection()->setMain(1));
        $this->assertTrue($file2->is_main);
        $this->assertFalse($file1->is_main);
        $this->assertFalse($file3->is_main);
    }

    /**
     * @test
     */
    public function getByName()
    {
        $this->syntheticModel->save();
        $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__FILE__);
        $file = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../img/logo.png');
        $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../bootstrap.php');
        $this->assertEquals($file, $this->syntheticModel->getAttachedFilesCollection()->getByName('logo.png'));
    }

    /**
     * @test
     */
    public function getGenerator()
    {
        $this->syntheticModel->save();
        $file1 = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__FILE__);
        $file2 = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../img/logo.png');
        $file3 = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../bootstrap.php');

        $generator = $this->syntheticModel->getAttachedFilesCollection()->getGenerator();
        $this->assertInstanceOf(Generator::class, $generator);
        $this->assertEquals($file1, $generator->current());
        $generator->next();
        $this->assertEquals($file2, $generator->current());
        $generator->next();
        $this->assertEquals($file3, $generator->current());
    }
}
