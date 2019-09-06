<?php

namespace app\controllers;

use Yii;
use yii\web\Controller;
use app\models\Tree;

class TreeController extends Controller
{
    /**
     * Generate new tree and .
     *
     * @return string
     */
    public function actionGenerate()
    {
        $user = Yii::$app->user;
        if($user->isGuest){
            $user->loginRequired();
        }

        $tree = Tree::getByUser();
        $tree->generate();

        return $this->goBack();
    }
}
