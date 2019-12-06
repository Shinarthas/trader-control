<?php

use yii\widgets\DetailView;

function removeqsvar($url, $varname) {
    list($urlpart, $qspart) = array_pad(explode('?', $url), 2, '');
    parse_str($qspart, $qsvars);
    unset($qsvars[$varname]);
    $newqs = http_build_query($qsvars);
    return $urlpart . '?' . $newqs;
}
?>
<form>
    <div class="row">
        <div class="col-md-12">
            <div class="col-md-2">Message</div>
            <div class="col-md-2">Type</div>
            <div class="col-md-2">From</div>
            <div class="col-md-2">Date From</div>
            <div class="col-md-2">Date to</div>
        </div>
        <div class="col-md-12">
            <div class="col-md-2"><input name="message" value="<?=isset($_GET['message'])?$_GET['message']:''?>" class="form-control" ></div>
            <div class="col-md-2">
                <select class="form-control" name="type">
                    <option  value="" <?=($_GET['type']=="")?'selected':''?> ></option>
                    <option  value="error" <?=($_GET['type']=="error")?'selected':''?> >Error</option>
                    <option  value="info" <?=($_GET['type']=="info")?'selected':''?> >Info</option>
                    <option  value="warning" <?=($_GET['type']=="warning")?'selected':''?> >Warning</option>
                    <option  value="log" <?=($_GET['type']=="log")?'selected':''?> >Log</option>
                </select>
            </div>
            <div class="col-md-2">
                <select class="form-control" name="from">
                    <option  value="" <?=($_GET['from']=="")?'selected':''?>></option>
                    <option  value="statistics" <?=($_GET['from']=="statistics")?'selected':''?>>Statistics</option>
                    <option  value="control" <?=($_GET['from']=="control")?'selected':''?>>Control</option>
                    <option  value="accounts"  <?=($_GET['from']=="accounts")?'selected':''?>>Accounts</option>
                </select>
            </div>
            <div class="col-md-2"><input type="date" value="<?=isset($_GET['date_from'])?$_GET['date_from']:''?>" name="date_from" class="form-control" ></div>
            <div class="col-md-2"><input type="date" value="<?=isset($_GET['date_to'])?$_GET['date_to']:''?>" name="date_to" class="form-control" ></div>
            <div class="col-md-2"><button class="btn btn-primary">Submit</button></div>
        </div>
        <div class="col-md-12">
            <?php $link= (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? "https" : "http") . "://$_SERVER[HTTP_HOST]"; ?>

            <?php for($i=0;$i<$count;$i+=\backend\controllers\LogsController::$limit){ ?>
                <?php
                $q=$_GET;
                $q['page']=($i/\backend\controllers\LogsController::$limit)+1;
                $url=$link.'/logs'.'?'.http_build_query($q);
            ?>
                <a class="btn btn-primary" href="<?= $url?>"><?= ($i/\backend\controllers\LogsController::$limit)+1 ?></a>
            <?php } ?>
        </div>
    </div>
</form>
<div class="row">
    <table class="table table-dark" >
        <thead>
        <tr>
            <th scope="col">#</th>
            <th scope="col">Body</th>
            <th scope="col">Message</th>
            <th scope="col">Type</th>
            <th scope="col">From</th>
            <th scope="col">Date Added</th>
        </tr>
        </thead>
        <tbody>

        <?php foreach ($logs as $log){?>
            <tr>
                <td ><?=$log->id?></td>
                <td class="break"><?= json_encode($log->info)?></td>
                <td><?= $log->message?></td>
                <td><?= $log->type?></td>
                <td><?= $log->from?></td>
                <td><?= $log->created_at?></td>
            </tr>
        <?php } ?>

        </tbody>
    </table>
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