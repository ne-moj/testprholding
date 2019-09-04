<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\Apples;

class ApplesController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $apples = Apples::find()->orderBy('created_at')->all();
        return $this->render('index', ['apples' => $apples]);
    }
}
