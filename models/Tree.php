<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Tree extends ActiveRecord
{
    public static function getByUser()
    {
        // TODO: return tree by user
        $tree = self::findOne(1);
        if($tree === null){
            $tree = new self();
        }

        return $tree;
    }

    public function __construct()
    {
        $this->width  = rand(100, 1000);
        $this->height = rand(100, 400);

        $this->save();
    }

    public function getDataByTree ()
    {
        $width = $this->width;
        $height = $this->height;

        return [
            'crownCenterPosX' => $width / 2,
            'crownCenterPosY' => $height,
            'crownWidth' => $width,
            'crownHeight' => $height,
            'halfCrownWidth' => $width / 2,
            'halfCrownHeigh' => $height / 2,
        ];
    }

    public function getUrlImage()
    {
        $relativePath = '/images/tree_' . $this->width . 'x' . $this->height . '.png';
        $pathToFile = Yii::getAlias('@webroot') . $relativePath;
        $urlPath = Yii::getAlias('@web') . $relativePath;

        if(!file_exists($pathToFile)){
            $image = new TreeImage(['width' => $this->width, 'height' => $this->height]);
            $image->create()->saveToFile($pathToFile, 'png');
        }

        return $urlPath;
    }
}
