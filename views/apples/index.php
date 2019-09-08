<?php
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Apples';
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/bootstrap.min.css', array('position' => $this::POS_HEAD), 'bootstrap');
$this->registerCssFile(Yii::$app->request->baseUrl . '/css/toast.min.css', array('position' => $this::POS_HEAD), 'toast');
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/jquery.min.js', array('position' => $this::POS_HEAD), 'jquery');
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/bootstrap.min.js', array('position' => $this::POS_HEAD), 'bootstrap');
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/bootbox.min.js', array('position' => $this::POS_END), 'bootbox');
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/bootbox.locales.min.js', array('position' => $this::POS_END), 'bootbox-locales');
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/toast.min.js', array('position' => $this::POS_END), 'toast');
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/main.js', array('position' => $this::POS_END), 'main');

$this->registerCss('
    .apple {
        width: 16px;
        height: 16px;
        border-width: 1px;
        border-style: solid;
        border-radius: 8px;
        border-color: white;
        position: absolute;
    }
    .modal-open .modal {
        display: flex !important;
        align-items: center;
        justify-content: center;
    }
');
?>
<div class="site-index">

    <div class="jumbotron">
        <h1>Яблоки</h1>

        <p class="lead">Поздравляем, Вы на странице яблок, они у нас самые разные :)</p>
        <?= Html::a('Сгенерировать новое дерево с яблоками', ['/tree/generate'], ['class'=>'btn btn-primary']) ?>
    </div>

    <div class="body-content">
<?php
    if(empty($apples)):
?>
        <div class="row">
            <div class="col-md-auto">
                <div class="alert alert-info alert-dismissible text-center">
                    Ой! Похоже что все яблоки были съедены, но Вы можете попробовать помедитировать, возможно они появяться.
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
            </div>
        </div>

<?php
    else:
?>
        <div class="row">
            <div style="float:left"><?= Html::img($treeImage) ?></div>
            <div style="display:flex; height:<?= $tree->height ?>px;">
                <div style="display:table; overflow: auto; padding: 20px">
                    <div style="display:table-cell; vertical-align:middle">
                        <h3>Условия</h3>
                        <ul>
                            <li>Пока яблоко висит на дереве - испортиться не может.</li>
                            <li>Когда яблоко висит на дереве - съесть не получится.</li>
                            <li>Чтобы сбить яблоко с дерева, нужно нажать на него мышью.</li>
                            <li>Чтобы съесть яблоко, нужно нажать на него мышью, когда оно на земле.</li>
                            <li>Если яблоко пролежало более 5 часов - оно испортилось.</li>
                            <li>Когда испорчено - съесть не получится, но его можно выбросить.</li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12" style="position:relative; left: -8px; top: -8px;">
<?php
        foreach($apples as $apple):
?>
                <a href="#" data-id="<?= $apple->id ?>" data-eaten="<?= $apple->eaten ?>" data-status="<?= $apple->status ?>" class='apple' style="background-color: <?= $apple->color ?>; top:-<?= $apple->pos_y; ?>px;left:<?= $apple->pos_x; ?>px"></a>
<?php
        endforeach;
?>
            </div>
        </div>
        <div style="display: none" id='apple-dialog'>
            <p>Это тестовое диалоговое окно для примера</p>
        </div>
<?php
    endif;
?>
    </div>
</div>
<?php
$script = "
    $('document').ready(function() {
        initialToast();
        $('.apple').click(function() {
            event.preventDefault()
            let apple = this;
            let posX = parseInt(apple.style.left);
            let posY = parseInt(apple.style.top);
            let status = $(apple).data('status');

            let rockingAppleId = 0;
            let success = null;
            let error = null;

            if(status == 'hanging'){
                // hanging
                let url = '" . Yii::$app->request->baseUrl . Url::to(['/ajax/knock-down-apple']) . "'
                rockingAppleId = animateRockingApple(apple);

                success = function (data) {
                    let result = JSON.parse(data);
                    posY = parseInt(apple.style.top);
                    apple.style.left = posX + 'px';
                    if(result.success){
                        cancelAnimation(rockingAppleId);
                        animateAppleDown(apple)
                        $(apple).data('status', 'lay');
                    }else{
                        showMyError(result.message ? result.message : 'appleNotDown');
                    }
                };
                error = function (xhr, ajaxOptions, thrownError) {
                    showMyError('appleNotDown');
                };

                ajaxToServer(url, {id: $(apple).data('id')}, success, error);
            }else if(status == 'lay'){
                let eaten = $(apple).data('eaten');
                let startVal = eaten + parseInt((100 - eaten) / 2);
                bootbox.prompt({
                    title: \"Съесть яблоко\",
                    message: \"<h2>Съедено '\" + eaten + \"%' яблока.</h2><p>Выберете сколько хотите съесть яблока:</p>\",
                    inputType: 'range',
                    min: eaten,
                    max: 100,
                    step: 1,
                    value: startVal,
                    callback: function (slice) {
                        if(slice === null){
                            // cancel
                            return ;
                        }

                        slice = slice - eaten;

                        let url = '" . Yii::$app->request->baseUrl . Url::to(['/ajax/eat-apple']) . "'
                        success = function (data) {
                            let result = JSON.parse(data);
                            if(result.success){
                                $(apple).data('eaten', slice);

                                if(eaten + slice >= 100){
                                    apple.style.display = 'none';
                                    showMySuccess('Поздравляем, вы съели яблоко!');
                                }else{
                                    showMySuccess('Поздравляем, вы откусили ' + slice + '% яблока!');
                                }
                            }else{
                                showMyError(result.message);
                            }
                        };
                        error = function (xhr, ajaxOptions, thrownError) {
                            showMyError('Что-то пошло не так... Яблоко оказывается слишком твердым для ваших зубов, вы так и не смогли откусить от него ни кусочка');
                        };
                        ajaxToServer(url, {id: $(apple).data('id'), size: slice}, success, error);
                    }
                });
            }else{
                // decayed
                bootbox.confirm({
                    message: \"Это сгнившее яблоко, его нельзя съесть! Выбросить его?\",
                    buttons: {
                        confirm: {
                            label: 'Да',
                            className: 'btn-success'
                        },
                        cancel: {
                            label: 'Нет',
                            className: 'btn-danger'
                        }
                    },
                    callback: function (result) {
                        if(result === false){
                            // cancel
                            return ;
                        }

                        let url = '" . Yii::$app->request->baseUrl . Url::to(['/ajax/remove-apple']) . "'

                        success = function (data) {
                            let result = JSON.parse(data);
                            if(result.success){
                                apple.style.display = 'none';

                                showMySuccess('Поздравляем, вы выбросили яблоко, теперь оно больше не побеспокоит Вас!');
                            }else{
                                showMyError(result.message);
                            }
                        };
                        error = function (xhr, ajaxOptions, thrownError) {
                            showMyError('Что-то пошло не так... Яблоко выскальзывает из ваших рук и продолжает лежать на земле');
                        };

                        ajaxToServer(url, {id: $(apple).data('id')}, success, error);
                    }
                });
            }
        });
        $('.test-ajax').click(function() {
        });
    });";

$this->registerJs($script);
?>
