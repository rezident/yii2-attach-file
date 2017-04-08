<?php


namespace rezident\attachfile\views\extra\image;


use yii\base\InvalidConfigException;

abstract class ImageResizer
{
    const RESIZE_MODE_CROP = 1;
    const RESIZE_MODE_STRETCH = 2;
    const RESIZE_MODE_PAD = 3;

    const PAD_COLOR_BLACK = '000000';
    const PAD_COLOR_WHITE = 'FFFFFF';

    const ANCHOR_LEFT_TOP = 1;
    const ANCHOR_CENTER_TOP = 2;
    const ANCHOR_RIGHT_TOP = 3;
    const ANCHOR_LEFT_CENTER = 4;
    const ANCHOR_CENTER_CENTER = 5;
    const ANCHOR_RIGHT_CENTER = 6;
    const ANCHOR_LEFT_BOTTOM = 7;
    const ANCHOR_CENTER_BOTTOM = 8;
    const ANCHOR_RIGHT_BOTTOM = 9;

    /**
     * @var int
     */
    protected $width;

    /**
     * @var int
     */
    protected $height;

    /**
     * @var int
     */
    protected $anchor;

    /**
     * @var string
     */
    protected $padColor;

    static private $resizerByResizeMode = [
        self::RESIZE_MODE_STRETCH => ImageResizerStretch::class,
        self::RESIZE_MODE_PAD => ImageResizerPad::class,
        self::RESIZE_MODE_CROP => ImageResizerCrop::class
    ];

    private function __construct($width = null, $height = null, $anchor = null, $padColor = null)
    {
        $this->width = $width;
        $this->height = $height;
        $this->anchor = $anchor;
        $this->padColor = $padColor;
    }

    /**
     * Creates new instance of the ImageResizer class
     *
     * @param int|null $width
     * @param int|null $height
     * @param int|null $resizeMode
     * @param int|null $anchor
     * @param string|null $padColor
     *
     * @return ImageResizer
     *
     * @throws InvalidConfigException
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    public static function create($width = null, $height = null, $resizeMode = null, $anchor = null, $padColor = null)
    {
        $className = null;
        if (self::isOneSizeSpecified($width, $height)) {
            $className = ImageResizerProportional::class;
        } else {
            if(isset(self::$resizerByResizeMode[$resizeMode])) {
                $className = self::$resizerByResizeMode[$resizeMode];
            }

        }

        if ($className) {
            return new $className($width, $height, $anchor, $padColor);
        }

        throw new InvalidConfigException();
    }

    /**
     * Returns a resized image
     *
     * @param resource $image Source of an image
     *
     * @return resource A resized image
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    abstract public function getResizedImage($image);

    /**
     * Returns whether only one size is specified
     *
     * @param int|null $width
     * @param int|null $height
     *
     * @return bool
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    private static function isOneSizeSpecified($width, $height)
    {
        return ($width != null && $height == null) || ($width == null && $height != null);
    }

    /**
     * Creates a new image
     *
     * @param int $width
     * @param int $height
     *
     * @return resource
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    protected function createImage($width, $height)
    {
        $result = imagecreatetruecolor($width, $height);
        imagesavealpha($result, true);
        $color = imagecolorallocatealpha($result, 255, 255, 255, 127);
        imagefill($result, 0, 0, $color);
        return $result;
    }


    /**
     * Returns the factors for resizing by the width and the height
     *
     * @param resource $image
     *
     * @return float[]
     *
     * @author Yuri Nazarenko / rezident <mail@rezident.org>
     */
    protected function getImageFactors($image)
    {
        $xFactor = imagesx($image) / $this->width;
        $yFactor = imagesy($image) / $this->height;
        return [$xFactor, $yFactor];
    }


}