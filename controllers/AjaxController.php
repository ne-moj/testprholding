<?php

namespace app\controllers;

use Yii;
use yii\web\NotFoundHttpException;
use yii\web\Response;
use yii\web\Controller;
use app\models\Apple;

class AjaxController extends Controller
{
    public function __construct($id, $module, $config = [])
    {
        if(!Yii::$app->request->isAjax){
//            throw new NotFoundHttpException();
        }

        Yii::$app->response->format = Response::FORMAT_JSON;

        parent::__construct($id, $module, $config);
    }

    /**
     * Generate new tree and .
     *
     * @return string
     */
    public function actionKnockDownApple()
    {
        $user = Yii::$app->user;
        if($user->isGuest){
            return $this->error('Вы должны войти в свой аккаунт для того чтобы совершать эти действия');
        }

        $data = Yii::$app->request->post();

        $apple = Apple::findOne($data['id']);
        if($apple === null){
            return $this->error("Яблока {$data['id']} не существует");
        }

        $apple->fallToGround();

        return $this->success();
    }

    public function success($message = '')
    {
        $data = [
            'success' => 1,
            'message' => $message,
        ];

        return json_encode($data);
    }

    public function error($message)
    {
        $data = [
            'error' => 1,
            'message' => $message,
        ];

        return json_encode($data);
    }
}
