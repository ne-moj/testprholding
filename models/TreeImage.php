<?php

namespace app\models;

class TreeImage
{
    private $image = null;

    public $backgroundAlpha = 127;
    public $backgroundColor = '#ffffff';
    public $trunkColor      = '#8b4513';
    public $crownColor      = '#006400';
    public $width           = 200;
    public $height          = 200;
    public $padding         = 0;

    public $smoothing       = 4;

    /*
     * @param array $params
     */
    public function __construct($params = array())
    {
        $this->width           = !empty($params['width'])           ? $params['width']           : $this->width;
        $this->height          = !empty($params['height'])          ? $params['height']          : $this->height;
        $this->padding         = !empty($params['padding'])         ? $params['padding']         : $this->padding;
        $this->backgroundColor = !empty($params['backgroundColor']) ? $params['backgroundColor'] : $this->backgroundColor;
        $this->backgroundAlpha = !empty($params['backgroundAlpha']) ? $params['backgroundAlpha'] : $this->backgroundAlpha;
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
     * @return Tree
     */
    public function saveToFile($file, $type = 'png')
    {
        switch($type){
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
                imagejpeg($this->image, $file);
                break;

            case 'png':
            default:
                imagepng($this->image, $file);
        }

        return $this;
    }

    /**
     * Create a tree image
     *
     * @return Tree
     */
    public function create()
    {
        $width           = $this->width;
        $height          = $this->height;
        $padding         = $this->padding;
        $backgroundColor = $this->backgroundColor;
        $backgroundAlpha = $this->backgroundAlpha;
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

        $centerTreeX2X = ($halfWidth + $padding) * $size2X;
        $centerTreeY2X = ($halfHeight + $padding) * $size2X;

        $widthTrunk2X = ((sqrt($halfWidth * $halfWidth + $halfHeight * $halfHeight) / 6) * $size2X);

        $trunk2X = [
            $centerTreeX2X - ($widthTrunk2X / 2), $heightImage2X,
            $centerTreeX2X, $centerTreeY2X,
            $centerTreeX2X + ($widthTrunk2X / 2), $heightImage2X,
        ];

        // create an empty image $size2X times larger than necessary
        $image2X = imagecreatetruecolor($widthImage2X, $heightImage2X);

        imagesavealpha($image2X, true);

        // set background color
        $bg = imagecolorallocatealpha($image2X, $this->getRedColor($backgroundColor), $this->getGreenColor($backgroundColor), $this->getBlueColor($backgroundColor), $backgroundAlpha);

        // set color for the trunk
        $colTrunk = imagecolorallocate($image2X, $this->getRedColor($trunkColor), $this->getGreenColor($trunkColor), $this->getBlueColor($trunkColor));

        // set color crown
        $colCrown = imagecolorallocate($image2X, $this->getRedColor($crownColor), $this->getGreenColor($crownColor), $this->getBlueColor($crownColor));

        // background fill
        imagefill($image2X, 0, 0, $bg);

        // create trunk
        imagefilledpolygon($image2X, $trunk2X, 3, $colTrunk);

        // create crown
        imagefilledellipse($image2X, $centerTreeX2X, $centerTreeY2X, $width * $size2X, $height * $size2X, $colCrown);

        // сompress the image to the desired size
        $this->image = imagecreatetruecolor($widthImage, $heightImage);
        imagesavealpha($this->image, true);
        imagefill($this->image, 0, 0, $bg);
        imagecopyresampled($this->image, $image2X, 0, 0, 0, 0, $widthImage, $heightImage, $widthImage2X, $heightImage2X);

        return $this;
    }

    /*
     * Get red color from hex value
     *
     * @param string $color (hex color)
     * @return integer
     */
    private function getRedColor ($color)
    {
        $hex = substr($color, 1, 2);

        return hexdec($hex);
    }

    /*
     * Get green color from hex value
     *
     * @param string $color (hex color)
     * @return integer
     */
    private function getGreenColor ($color)
    {
        $hex = substr($color, 3, 2);

        return hexdec($hex);
    }

    /*
     * Get blue color from hex value
     *
     * @param string $color (hex color)
     * @return integer
     */
    private function getBlueColor ($color)
    {
        $hex = substr($color, 5, 2);

        return hexdec($hex);
    }
}
