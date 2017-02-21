<?php


namespace rezident\attachfile\views;


use rezident\attachfile\exceptions\SettingsIsIncorrect;
use rezident\attachfile\models\AttachedFile;

class ViewsFactory
{
    /**
     * @var AttachedFile
     */
    private $attachedFile;

    function __construct(AttachedFile $attachedFile)
    {
        $this->attachedFile = $attachedFile;
    }

    /**
     * @return ImageView
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public function asImage()
    {
        return new ImageView($this->attachedFile);
    }

    public function asRaw()
    {
        return new RawView($this->attachedFile);
    }

    public function bySettings($settings)
    {
        $settingsParts = explode(RawView::SETTINGS_SEPARATOR, $settings);
        $methodName = 'as' . ucfirst(array_shift($settingsParts));
        if(method_exists($this, $methodName) == false) {
            throw new SettingsIsIncorrect('Method "' . $methodName . '" does not exist');
        }

        /** @var RawView $view */
        $view = call_user_func([$this, $methodName]);
        $view->setSettingsArray($settingsParts);
        return $view;
    }
}