<?php


namespace rezident\attachfile\views;


use ReflectionClass;
use ReflectionProperty;
use rezident\attachfile\AttachFileModule;
use rezident\attachfile\extensions\DateTime;
use rezident\attachfile\models\AttachedFile;
use rezident\attachfile\models\AttachedFileView;
use yii\base\Object;
use yii\helpers\FileHelper;
use yii\helpers\Json;

abstract class AbstractView extends Object
{
    /**
     * @var AttachedFile
     */
    protected $attachedFile;

    /**
     * Returns the URL of the file
     *
     * @return string
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getUrl()
    {
        $attachedFileView = $this->getAttachedFileView();
        return $this->getUrlPath() . '/' . $this->getFileName($attachedFileView);
    }

    /**
     * Returns the content of the file
     *
     * @param AttachedFileView $attachedFileView
     *
     * @return string
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getContent(AttachedFileView $attachedFileView)
    {
        $viewFilePath = $this->getViewFilePath($attachedFileView);
        if(file_exists($viewFilePath)) {
            return file_get_contents($viewFilePath);
        }

        ob_start();
        $this->render();
        $content = ob_get_clean();
        FileHelper::createDirectory(dirname($viewFilePath));
        file_put_contents($viewFilePath, $content);
        return $content;
    }

    /**
     * Returns content type of the file
     *
     * @return string
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getContentType()
    {
        return $this->attachedFile->getMimeType();
    }

    /**
     * @param AttachedFile $attachedFile
     */
    public function setAttachedFile($attachedFile)
    {
        $this->attachedFile = $attachedFile;
    }

    /**
     * Renders a view
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    abstract protected function render();

    /**
     * Returns an extension of the file
     *
     * @return string
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    protected function getExtension()
    {
        return $this->attachedFile->getExtension();
    }

    /**
     * @return string
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function getViewPath()
    {
        $result = rtrim(\Yii::getAlias(AttachFileModule::getInstance()->viewsPath), '/');
        $result .= '/';
        $result .= $this->getViewDirPath();
        return $result;
    }

    /**
     * @return string
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function getUrlPath()
    {
        $result = rtrim(\Yii::getAlias(AttachFileModule::getInstance()->webPrefix), '/');
        $result .= '/';
        $result .= $this->getViewDirPath();
        return $result;
    }

    /**
     * @param AttachedFileView $attachedFileView
     *
     * @return string
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function getFileName(AttachedFileView $attachedFileView)
    {
        $result = $attachedFileView->id;
        $result .= '_';
        $result .= pathinfo($this->attachedFile->name, PATHINFO_FILENAME);
        if($this->getExtension()) {
            $result .= '.';
            $result .= $this->getExtension();

        }

        return $result;
    }

    /**
     * @return string
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function getConfigString()
    {
        $config = [
            'class' => get_called_class()
        ];
        $reflectionClass = new ReflectionClass($this);
        foreach ($reflectionClass->getProperties(ReflectionProperty::IS_PUBLIC) as $reflectionProperty) {
            $propertyName = $reflectionProperty->name;
            $config[$propertyName] = $this->$propertyName;
        }

        return Json::encode($config);
    }

    /**
     * @param string $configString
     *
     * @return string
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function getConfigStringHash($configString)
    {
        return mb_substr(md5($configString), 0, 8);
    }

    /**
     * @return AttachedFileView
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function getAttachedFileView()
    {
        $configString = $this->getConfigString();
        $attachFileView = AttachedFileView::find()
            ->byAttachedFile($this->attachedFile)
            ->byViewConfigHash($this->getConfigStringHash($configString))
            ->joinWith('attachedFile')
            ->one();

        if ($attachFileView == null) {
            $attachFileView = new AttachedFileView();
            $attachFileView->attached_file_id = $this->attachedFile->id;
            $attachFileView->view_config = $configString;
            $attachFileView->view_config_hash = $this->getConfigStringHash($configString);
            $attachFileView->extension = $this->getExtension();
            $attachFileView->created_at = (new DateTime())->getMysqlDateTimeString();
            $attachFileView->save();
        }

        return $attachFileView;
    }

    /**
     * @return string
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function getViewDirPath()
    {
        $result = $this->attachedFile->model_key;
        $result .= '/';
        $result .= mb_substr($this->attachedFile->md5_hash, 0, 3);
        return $result;
    }

    /**
     *
     * @param AttachedFileView $attachedFileView
     * @return string
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function getViewFilePath(AttachedFileView $attachedFileView)
    {
        return $this->getViewPath() . '/' . $this->getFileName($attachedFileView);
    }
}