<?php

namespace app\models;

use Yii;

/**
 * LoginForm is the model behind the login form.
 *
 * @property User|null $user This property is read-only.
 *
 */
class TreeImage
{
    private $image = null;

    public $backgroundColor = '#f0ffff';
    public $trunkColor      = '#8b4513';
    public $crownColor      = '#006400';
    public $width           = 200;
    public $height          = 200;
    public $padding         = 20;

    public $smoothing       = 4;

    public function __construct($params = array())
    {
        $this->width           = !empty($params['width'])           ? $params['width']           : $this->width;
        $this->height          = !empty($params['height'])          ? $params['height']          : $this->height;
        $this->padding         = !empty($params['padding'])         ? $params['padding']         : $this->padding;
        $this->backgroundColor = !empty($params['backgroundColor']) ? $params['backgroundColor'] : $this->backgroundColor;
        $this->trunkColor      = !empty($params['trunkColor'])      ? $params['trunkColor']      : $this->trunkColor;
        $this->crownColor      = !empty($params['crownColor'])      ? $params['crownColor']      : $this->crownColor;
    }

    public function __destruct()
    {
        if($this->image !== null){
            imagedestroy($this->image);
        }
    }

    /**
     * Save image to file
     *
     * @param string $file
     * @param string $type
     */
    public function saveToFile($file, $type = 'jpg')
    {
        switch($type){
            case 'png':
                imagepng($this->image, $file);
                break;

            case 'gif':
                imagegif($this->image, $file);
                break;

            case 'wbmp':
                imagewbmp($this->image, $file);
                break;

            case 'webp':
                imagewebp($this->image, $file);
                break;

            case 'jpg':
            case 'jpeg':
            default:
                imagejpeg($this->image, $file);
        }
    }

    /**
     * Create a tree
     */
    public function create()
    {
        $width           = $this->width;
        $height          = $this->height;
        $padding         = $this->padding;
        $backgroundColor = $this->backgroundColor;
        $trunkColor      = $this->trunkColor;
        $crownColor      = $this->crownColor;
        $halfWidth       = $width / 2;
        $halfHeight      = $height / 2;
        $heightTrunk     = $halfHeight;

        // The parameter indicates how many times the image is enlarged
        $size2X = $this->smoothing;

        $widthImage  = $width + $padding * 2;
        $heightImage = $height + $heightTrunk + $padding;

        $widthImage2X = $widthImage * $size2X;
        $heightImage2X = $heightImage * $size2X;

        $centerTreeX2X = ($width + $padding) * $size2X;
        $centerTreeY2X = ($height + $padding) * $size2X;

        $widthTrunk2X = ((sqrt($halfWidth * $halfWidth + $halfHeight * $halfHeight) / 6) * $size2X);

        $trunk2X = [
            $centerTreeX2X - ($widthTrunk2X / 2), $heightImage2X,
            $centerTreeX2X, $centerTreeY2X,
            $centerTreeX2X + ($widthTrunk2X / 2), $heightImage2X,
        ];

        // create an empty image $size2X times larger than necessary
        $image2X = imagecreatetruecolor($widthImage2X, $heightImage2X);

        // set background color
        $bg = imagecolorallocate($image2X, $this->getRedColor($backgroundColor), $this->getGreenColor($backgroundColor), $this->getBlueColor($backgroundColor));

        // set color for the trunk
        $colTrunk = imagecolorallocate($image2X, $this->getRedColor($trunkColor), $this->getGreenColor($trunkColor), $this->getBlueColor($trunkColor));

        // set color crown
        $colCrown = imagecolorallocate($image2X, $this->getRedColor($crownColor), $this->getGreenColor($crownColor), $this->getBlueColor($crownColor));

        // background fill
        imagefilledrectangle($image2X, 0, 0, $widthImage2X - 1, $heightImage2X - 1, $bg);

        // create trunk
        imagefilledpolygon($image2X, $trunk2X, 3, $colTrunk);

        // create crown
        imagefilledellipse($image2X, $centerTreeX2X, $centerTreeY2X, $width * $size2X, $height * $size2X, $colCrown);

        // сompress the image to the desired size
        $this->image = imagecreatetruecolor($widthImage, $heightImage);
        imagecopyresampled($this->image, $image2X, 0, 0, 0, 0, $widthImage, $heightImage, $widthImage2X, $heightImage2X);

        return $this;
    }

    private function getRedColor ($color)
    {
        $hex = substr($color, 1, 2);

        return hexdec($hex);
    }

    private function getGreenColor ($color)
    {
        $hex = substr($color, 3, 2);

        return hexdec($hex);
    }

    private function getBlueColor ($color)
    {
        $hex = substr($color, 5, 2);

        return hexdec($hex);
    }
}
