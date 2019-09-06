<?php
use yii\helpers\Url;
use yii\helpers\Html;

$this->title = 'Apples';
$this->registerJsFile(Yii::$app->request->baseUrl . '/js/jquery.min.js', array('position' => $this::POS_HEAD), 'jquery');

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
        <div class="row">
            <div class="col-lg-12" style="position:relative; left: 8px; top: -8px;">
<?php
        foreach($apples as $apple):
?>
                <div data-id="<?= $apple->id ?>" class='apple' style="background-color: <?= $apple->color ?>; top:-<?= $apple->pos_y; ?>px;left:<?= $apple->pos_x; ?>px"></div>
<?php
        endforeach;
?>
            </div>
        </div>
<?php
    endif;
?>
    </div>
</div>
<?php
$script = "
    function animate(options) {
        let start = performance.now();

        requestAnimationFrame(function animate(time) {
            // timeFraction от 0 до 1
            let timeFraction = (time - start) / options.duration;
            if (timeFraction > 1) timeFraction = 1;

            // текущее состояние анимации
            let progress = options.timing(timeFraction)
            
            options.draw(progress);

            if (timeFraction < 1) {
            requestAnimationFrame(animate);
            }

        });
    }
    $('document').ready(function() {
        $('.apple').click(function() {
            let apple = this;
            let appleDiv = this;
            let posX = parseInt(appleDiv.style.top);
            let posY = parseInt(appleDiv.style.left);

            let runLoadingAnimate = true;
            let loadingPosY = 0;
            let incriment = Math.random() > 0.5 ? 1 : -1;
            animate({
                duration: 10000,
                timing: function (timeFraction) {
                    return timeFraction;
                },
                draw: function (progress) {
                    if(runLoadingAnimate){
                        if(loadingPosY < -5){
                            incriment = 1;
                        }else if(loadingPosY > 5){
                            incriment = -1;
                        }
                        loadingPosY += incriment;

                        appleDiv.style.left = posY + loadingPosY + 'px';
                        appleDiv.style.top = posX - Math.abs(loadingPosY) + 'px';
                    }
                }

            });

            $.ajax({
                url: '" . Yii::$app->request->baseUrl . Url::to(['/ajax/knock-down-apple']) . "',
                type: 'post',
                data: {
                    id : $(apple).data('id'),
                    _crsf : '" . Yii::$app->request->getCsrfToken() . "'
                },
                success: function (data) {
                    runLoadingAnimate = false;
                    appleDiv.style.left = posY + 'px';

                    animate({
                        duration: 1000,
                        timing: function (timeFraction) {
                            return Math.pow(timeFraction, 10);
                        },
                        draw: function (progress) {
                            appleDiv.style.top = posX - progress * posX + 'px';
                        }

                    });
                },
                error: function (xhr, ajaxOptions, thrownError) {
                    console.log(xhr.status);
                    console.log(thrownError);
                }
            });

        });
        $('.test-ajax').click(function() {
        });
    });";

$this->registerJs($script);
?>
