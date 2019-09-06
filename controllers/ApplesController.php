<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Apple;
use app\models\Tree;

class ApplesController extends Controller
{
    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        $user = Yii::$app->user;
        if($user->isGuest){
            $user->loginRequired();
        }

        $apples = Apple::find()->orderBy('created_at')->all();
        $tree = Tree::getByUser();
        $treeImage = $tree->getUrlImage();
        
        return $this->render('index', compact('apples', 'tree', 'treeImage'));
    }


    public function actionGenerate()
    {
        Apple::generateApples();

        return $this->actionIndex();
    }
}
