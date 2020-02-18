<?php
?>
<p>
    <pre>
    <?php //echo $info;?>
</pre>
</p>
<div class="log">
    <p>Procedure started</p>
</div>

<script>
    function addP(text) {
        $('.log').append("<p>"+text+"</p>")
    }

    setTimeout(addP,1000,'retriving orders')
    setTimeout(addP,2000,'retriving statistics')
    setTimeout(addP,2100,'canceling orders')
    setTimeout(addP,3100,'canceled')
    setTimeout(addP,4100,'calculating  market price')
    setTimeout(addP,4100,'creating sell  orders')
    setTimeout(addP,6100,'done')
</script>
