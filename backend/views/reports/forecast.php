<?php
/**
 * Created by PhpStorm.
 * User: 1
 * Date: 23.03.2020
 * Time: 17:29
 */
?>

<h2>Recent Forecasts</h2>
<div class="row">
    <?php foreach ($forecasts as $forecast){ ?>
        <div class="col-md-4">
            <?= $this->render('/partials/_forecast', ['forecast'=>$forecast], true)?>
        </div>

    <?php } ?>
</div>


<style>
    .panel-default > .panel-heading {
        color: #ffffff;
        background-color: #1f2121;
        border-color: #c3c3c3;
    }
    .panel-footer {

        background-color: #3d403f;

    }
    .panel {
        margin-bottom: 20px;
        background-color: #1e1f1f;
    }
    span.total{
        color: #4dd415;
        font-weight: 700;
        font-size: 20px;
    }
    .dropdown-item{
        display: block;
    }
    .dropdown-menu {
        background: #222424;
        padding: 5px;
        border: 1px solid;
    }
    .panel-body ul{
        margin-left: 30px;
    }
</style>