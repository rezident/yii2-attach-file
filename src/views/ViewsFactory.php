<?php


namespace rezident\attachfile\views;


use rezident\attachfile\exceptions\ViewNotFoundException;
use rezident\attachfile\models\AttachedFile;

/**
 * Class ViewsFactory
 * @author Yuri Nazarenko / rezident <mail@rezident.org>
 *
 * @method RawView raw
 * @method JpgView jpg
 * @method PngView png
 */
class ViewsFactory
{
    /**
     * @var AttachedFile
     */
    private $attachedFile;

    /**
     * ViewsFactory constructor.
     *
     * @param AttachedFile $attachedFile
     */
    private function __construct($attachedFile)
    {
        $this->attachedFile = $attachedFile;
    }

    /**
     * Creates a new instance of the class
     *
     * @param AttachedFile|null $attachedFile
     *
     * @return ViewsFactory
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public static function getInstance($attachedFile = null)
    {
        return new static($attachedFile);
    }

    /**
     * Processes request of a specified view
     *
     * @param string $name
     * @param array $arguments
     *
     * @return AbstractView
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    function __call($name, $arguments)
    {
        return $this->getView($name);
    }

    /**
     * @param string $name
     *
     * @return AbstractView
     *
     * @throws ViewNotFoundException
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private function getView($name)
    {
        $shortClassName = ucfirst($name) . 'View';
        $classNameParts = explode('\\', self::class);
        $lastPosition = count($classNameParts) - 1;
        $classNameParts[$lastPosition] = $shortClassName;
        $className = implode('\\', $classNameParts);
        if (class_exists($className)) {
            return new $className(['attachedFile' => $this->attachedFile]);
        }

        throw new ViewNotFoundException($name);
    }
}