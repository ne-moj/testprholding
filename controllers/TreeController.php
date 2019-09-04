<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\TreeImage;

class TreeController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex($width = 200, $height = 150)
    {
        $urlPath = $this->getUrlTree($width, $height);
        return $this->render('index', ['image' => $urlPath]);
    }

    /**
     * Get url for url by width-height
     *
     * @param int $width
     * @param int $height
     * @return string
     */
    private function getUrlTree($width, $height)
    {
        $width = (int)$width;
        $height = (int)$height;
        $relativePath = '/images/tree_' . $width . 'x' . $height . '300.jpg';
        $pathToFile = Yii::getAlias('@webroot') . $relativePath;
        $urlPath = Yii::getAlias('@web') . $relativePath;

        if(!file_exists($pathToFile)){
            $image = new TreeImage(['width' => $width, 'height' => $height]);
            $image->create()->saveToFile($pathToFile);
        }

        return $urlPath;
    }
}
