<?php

/* @var $this yii\web\View */

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
        foreach($apples as $apple):
?>
        <div class="row">
            <div class="col-lg-12">
                <?php var_dump($apple); ?>
            </div>
        </div>
<?php
        endforeach;
?>
<?php
    endif;
?>
    </div>
</div>
