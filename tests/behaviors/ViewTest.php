<?php


namespace rezident\attachfile\tests\behaviors;


use PHPUnit\Framework\TestCase;
use rezident\attachfile\attachers\LocalFileAttacher;
use rezident\attachfile\models\AttachedFile;
use rezident\attachfile\models\AttachedFileView;
use rezident\attachfile\tests\SyntheticModel;
use rezident\attachfile\views\extra\image\ImageResizer;
use rezident\attachfile\views\JpgView;
use rezident\attachfile\views\RawView;
use yii\helpers\FileHelper;

class ViewTest extends TestCase
{
    /**
     * @var SyntheticModel
     */
    private $syntheticModel;

    public function setUp()
    {
        \Yii::setAlias('@web', '');
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
    public function returnViews()
    {
        $file = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__FILE__);
        $this->assertInstanceOf(RawView::class, $file->getView()->raw());
        $this->assertInstanceOf(JpgView::class, $file->getView()->jpg());
    }

    /**
     * @test
     */
    public function returnUrl()
    {
        $file = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__FILE__);
        $url = $file->getView()->raw()->getUrl();
        $this->assertStringEndsWith('.php', $url);
    }

    /**
     * @test
     */
    public function createRawViewFile()
    {
        $file = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__FILE__);
        $view = $file->getView()->raw();
        $view->getUrl();
        $this->assertEquals($view->getContent(AttachedFileView::find()->one()), file_get_contents(__FILE__));
    }


    /**
     * @test
     */
    public function createImageViewFileProportional()
    {
        $file = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../img/logo.png');
        $view = $file->getView()->png()
            ->height(400);
        $view->getUrl();
        $view->getContent(AttachedFileView::find()->one());
//        die;
    }

    /**
     * @test
     */
    public function createImageViewFileWithoutResize()
    {
        $file = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../img/logo.png');
        $view = $file->getView()->png();
        $view->getUrl();
        $view->getContent(AttachedFileView::find()->one());
    }

    /**
     * @test
     */
    public function createImageViewFileStretch()
    {
        $file = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../img/logo.png');
        $view = $file->getView()->png()
            ->height(400)
            ->width(400)
            ->resizeMode(ImageResizer::RESIZE_MODE_STRETCH);
        $view->getUrl();
        $view->getContent(AttachedFileView::find()->one());
    }

    /**
     * @test
     */
    public function createImageViewFileWithPads()
    {
        $file = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../img/logo.png');
        $view = $file->getView()->png()
            ->width(40)
            ->height(40)
            ->resizeMode(ImageResizer::RESIZE_MODE_PAD)
            ->padColor('FFAF00');
        $view->getUrl();
        $view->getContent(AttachedFileView::find()->one());
    }

    /**
     * @test
     */
    public function createImageViewFileWithCropByWidth()
    {
        $file = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../img/logo.png');
        $view = $file->getView()->png()
            ->width(60)
            ->height(60)
            ->resizeMode(ImageResizer::RESIZE_MODE_CROP)
            ->anchor(ImageResizer::ANCHOR_RIGHT_CENTER);
        $view->getUrl();
        $view->getContent(AttachedFileView::find()->one());
    }

    /**
     * @test
     */
    public function createImageViewFileWithCropByHeight()
    {
        $file = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../img/logo.png');
        $view = $file->getView()->png()
            ->width(260)
            ->height(30)
            ->resizeMode(ImageResizer::RESIZE_MODE_CROP)
            ->anchor(ImageResizer::ANCHOR_RIGHT_BOTTOM);
        $view->getUrl();
        $view->getContent(AttachedFileView::find()->one());

    }

    /**
     * @test
     */
    public function createViewsAndDeleteAttachedFile()
    {
        $file = $this->syntheticModel->getAttacher(LocalFileAttacher::class)->attach(__DIR__ . '/../img/logo.png');
        $view = $file->getView()->png();
        $view->getUrl();
        $view->getContent(AttachedFileView::find()->byId(1)->one());
        $view = $file->getView()->raw();
        $view->getUrl();
        $view->getContent(AttachedFileView::find()->byId(2)->one());

        $collection = $this->syntheticModel->getAttachedFilesCollection();
        $this->assertTrue($collection->delete($file));
        $this->assertCount(0, AttachedFileView::find()->all());

        $this->assertFileNotExists(__DIR__ . '/../../src/files/originals/synthetic/' . $file->md5_hash);
    }

}