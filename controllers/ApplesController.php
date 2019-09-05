<?php

namespace app\controllers;

use yii\web\Controller;
use app\models\Apple;

class ApplesController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $apples = Apple::find()->orderBy('created_at')->all();
        $tree = Apple::getTree();
        
        return $this->render('index', ['apples' => $apples, 'treeImage' => $tree->getUrlImage()]);
    }

    /**
     * Test apple functionality
     *
     * @return string
     */
    public function actionTestApple()
    {
        //Apple::generateApples();
    }

    public function actionGenerate()
    {
        Apple::generateApples();

        return $this->actionIndex();
    }
}
