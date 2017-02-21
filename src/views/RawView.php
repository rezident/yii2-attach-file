<?php


namespace rezident\attachfile\views;


use DateTime;
use rezident\attachfile\models\AttachedFile;
use rezident\attachfile\models\AttachedFileView;

class RawView
{
    const SETTINGS_SEPARATOR = '-';

    /**
     * @var AttachedFile
     */
    protected $attachedFile;

    /**
     * @var AttachedFileView[]
     */
    private $attachedFileViews = [];

    function __construct(AttachedFile $attachedFile)
    {
        $this->attachedFile = $attachedFile;
    }

    public function getUrl()
    {
        return $this->getAttachedFileView()->getUrl($this->attachedFile, $this->getOutputExtension());
    }

    public function getAttachedFileView()
    {
        $settings = $this->getSettings();
        if(isset($this->attachedFileViews[$settings]) == false) {
            $attachedFileView = AttachedFileView::find()->byAttachedFile($this->attachedFile)->bySettings($settings)->one();
            if($attachedFileView == null) {
                $attachedFileView = new AttachedFileView();
                $attachedFileView->attached_file_id = $this->attachedFile->id;
                $attachedFileView->settings = $settings;
                $attachedFileView->created_at = (new DateTime())->format('Y-m-d H:i:s');
                $attachedFileView->save();
            }

            $this->attachedFileViews[$settings] = $attachedFileView;
        }

        return $this->attachedFileViews[$settings];
    }

    public function setSettingsArray(array $settings)
    {
        // It does nothing :)
    }

    public function getContent($path)
    {
        ob_start();
        $this->echoContent($path);
        return ob_get_clean();
    }

    public function getOutputExtension()
    {
        return null;
    }

    protected function echoContent($path)
    {
        echo file_get_contents($path);
    }

    protected function getSettingsArray()
    {
        return [];
    }

    private function getViewName()
    {
        $shortClassName = array_reverse(explode('\\', get_called_class()))[0];
        return mb_strtolower(preg_replace('/View$/', '', $shortClassName));
    }


    private function getSettings()
    {
        $settings = array_merge([$this->getViewName()], $this->getSettingsArray());
        return trim(implode(self::SETTINGS_SEPARATOR, $settings), self::SETTINGS_SEPARATOR);
    }
}