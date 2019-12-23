<?php
//print_r($trading_pairs);

?>
<form method="post">
    <div class="row">
        <div  class="col-md-6">
            <?php foreach ($trading_pairs as $tp){ ?>
                <div class="col-md-12">
                    <?= $tp->trading_paid ?> - <input class="form-group" name="<?= $tp->trading_paid ?>" value="<?= $tp->rating; ?>">
                </div>
            <?php } ?>
        </div>
        <div  class="col-md-6">
            <button class="btn btn-primary">Save</button>
        </div>

    </div>
</form>

