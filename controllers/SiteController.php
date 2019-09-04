<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;

class SiteController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'only' => ['logout'],
                'rules' => [
                    [
                        'actions' => ['logout'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'logout' => ['post'],
                ],
            ],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Show tree
     *
     * @param string $message
     */
    public function actionShowTree($width = 300, $height = 400)
    {
        $urlPathToImage = $this->getUrlAddressToTreeImage((int) $width, (int) $height);

        return $this->render('tree', ['image' => $urlPathToImage]);
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        $model->password = '';
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }

    /**
     * Create a tree
     *
     * @param int $width
     * @param int $height
     */
    public function createTree($width = 300, $height = 200, $padding = 30)
    {
        $relativePathToImage = '/images/tree' . $width . 'x' . $height . '.jpg';

        $pathToImage = Yii::getAlias('@webroot') . $relativePathToImage;

        $colors = [
            'background' => [240, 255, 255],
            'trunk'      => [139, 69, 19],
            'crown'      => [0, 100, 0],
        ];

        // The parameter indicates how many times the image is enlarged
        $size2X = 4;

        $widthImage  = $width * 2 + $padding * 2;
        $heightImage = $height * 3 + $padding;

        $widthImage2X = $widthImage * $size2X;
        $heightImage2X = $heightImage * $size2X;

        $centerTreeX2X = ($width + $padding) * $size2X;
        $centerTreeY2X = ($height + $padding) * $size2X;

        $widthTrunk2X = ((sqrt($width * $width + $height * $height) / 5) * $size2X);

        $trunk2X = [
            $centerTreeX2X - ($widthTrunk2X / 2), $heightImage2X,
            $centerTreeX2X, $centerTreeY2X,
            $centerTreeX2X + ($widthTrunk2X / 2), $heightImage2X,
        ];

        // create an empty image $size2X times larger than necessary
        $image2X = imagecreatetruecolor($widthImage2X, $heightImage2X);

        // set background color
        $bg = imagecolorallocate($image2X, $colors['background'][0], $colors['background'][1], $colors['background'][2]);

        // set color for the trunk
        $colTrunk = imagecolorallocate($image2X, $colors['trunk'][0], $colors['trunk'][1], $colors['trunk'][2]);

        // set color crown
        $colCrown = imagecolorallocate($image2X, $colors['crown'][0], $colors['crown'][1], $colors['crown'][2]);

        // background fill
        imagefilledrectangle($image2X, 0, 0, $widthImage2X - 1, $heightImage2X - 1, $bg);

        // create trunk
        imagefilledpolygon($image2X, $trunk2X, 3, $colTrunk);

        // create crown
        imagefilledellipse($image2X, $centerTreeX2X, $centerTreeY2X, ($width * 2) * $size2X, ($height * 2) * $size2X, $colCrown);

        // сompress the image to the desired size
        $imageOut = imagecreatetruecolor($widthImage, $heightImage);
        imagecopyresampled($imageOut, $image2X, 0, 0, 0, 0, $widthImage, $heightImage, $widthImage2X, $heightImage2X);

        imagejpeg($imageOut, $pathToImage);
        imagedestroy($imageOut);
    }

    public function getUrlAddressToTreeImage($width, $height)
    {
        $relativePathToImage = '/images/tree' . $width . 'x' . $height . '.jpg';
        $pathToImage = Yii::getAlias('@webroot') . $relativePathToImage;
        $urlPathToImage = Yii::getAlias('@web') . $relativePathToImage;

        if(!file_exists($pathToImage)){
            $this->createTree($width, $height);
        }

        return $urlPathToImage;
    }
}
