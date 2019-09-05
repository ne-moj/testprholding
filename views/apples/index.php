<?php
use Yii;
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
        <?= Html::img($treeImage) ?>
        <div class="row">
            <div class="col-lg-12" style="position:relative; left: 8px; top: -8px;">
<?php
        foreach($apples as $apple):
?>
                <div class='apple' style="background-color: <?= $apple->color ?>; top:-<?= $apple->pos_y; ?>px;left:<?= $apple->pos_x; ?>px"></div>
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
<script>
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
            let appleDiv = this;
            let posX = parseInt(appleDiv.style.top);

            console.log(posX);

            animate({
                duration: 1000,
                timing: function (timeFraction) {
                    return Math.pow(timeFraction, 10);
                },
                draw: function (progress) {
                    appleDiv.style.top = posX - progress * posX + 'px';
                }

            });
        });
    });
</script>
