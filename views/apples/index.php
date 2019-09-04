<?php

/* @var $this yii\web\View */
use yii\helpers\Html;

$this->title = 'Apples';
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
        <?= Html::img('/images/tree_200x500.png') ?>
        <div class="row">
            <div class="col-lg-12">
<?php
        foreach($apples as $apple):
?>
<div class='apple' style="background-color: <?= $apple->color ?>; width:16px; height:16px; border-radius:8px;position:absolute;top:-<?= $apple->pos_y + 8; ?>px;left:<?= $apple->pos_x + 8; ?>px"></div>
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
