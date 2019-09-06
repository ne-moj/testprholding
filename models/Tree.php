<?php

namespace app\models;

use yii\db\ActiveRecord;
use Yii;

class Tree extends ActiveRecord
{
    public static function getByUser()
    {
        // TODO: return tree by user
        $tree = self::findOne(['user_id' => (int) Yii::$app->user->getId()]);
        if($tree === null){
            $tree = new self();
            $tree->generateTree();
        }

        return $tree;
    }

    public function generate()
    {
        $this->generateTree()->generateApples();

        return $this;
    }

    public function generateTree()
    {
        $this->user_id = (int) Yii::$app->user->getId();
        $this->width  = rand(100, 1000);
        $this->height = rand(100, 400);

        $this->save();

        return $this;
    }

    public function generateApples()
    {
        $factor = 75;

        $halfCrownWidth = $this->width / 2;
        $halfCrownHeight = $this->height / 2;

        $countApples = (int) ((pow($halfCrownWidth, 2) + pow($halfCrownHeight, 2)) / pow($factor, 2));

        Apple::regenerateApples($countApples);

        return $this;
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
            'halfCrownHeight' => $height / 2,
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
