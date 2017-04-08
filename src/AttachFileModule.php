<?php


namespace rezident\attachfile;


use yii\base\InvalidConfigException;
use yii\base\Module;

class AttachFileModule extends Module
{

    /**
     * Path, where original files will be stored
     * @var string
     */
    public $originalsPath;

    /**
     * Path, where views will be stored
     * @var string
     */
    public $viewsPath;

    /**
     * Url prefix for views in an url
     * @var string
     */
    public $webPrefix;

    /**
     * @var AttachFileModule
     */
    private static $instance;

    /**
     * Returns the attach file module
     *
     * @return AttachFileModule
     *
     * @throws InvalidConfigException If you didn't add module settings to config
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public static function getInstance()
    {
        if (isset(self::$instance) == false) {
            self::$instance = \Yii::$app->getModule('attach_file');
        }

        if (isset(self::$instance) == false) {
            throw new InvalidConfigException('The attach_file module not found. You should add it to config');
        }

        return self::$instance;
    }

}