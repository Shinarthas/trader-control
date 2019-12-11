<?php
/* @var $this yii\web\View */
/* @var $possibility array */

use yii\bootstrap\ActiveForm;
use yii\web\View;

/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

?>

<div class="row">
    <div class="col-md-4">
        <table class="table table-dark" >
            <thead>
            <tr>
                <th scope="col">#</th>
                <th scope="col">Name</th>
                <th scope="col">Possibility</th>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($possibility as $p){?>
                <tr>
                    <td ><?=$p->id?></td>
                    <td><?= $p->name?></td>
                    <td><?= $p->chance?></td>
                </tr>
            <?php } ?>

            </tbody>
        </table>
    </div>

</div>

<style>
    .table{
        background: #212529 !important;
        color: white !important;
    }
    td.break{
        word-break:break-all;
    }
</style>
