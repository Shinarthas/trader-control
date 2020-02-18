<?php
?>
<div class="log">
    <p>Procedure started</p>
</div>

<script>
    function addP(text) {
        $('.log').append("<p>"+text+"</p>")
    }

    setTimeout(addP,1000,'retriving orders')
    setTimeout(addP,2000,'retriving statistics')

    setTimeout(addP,4100,'calculating  market price')
    setTimeout(addP,4100,'creating sell BTC orders')
    setTimeout(addP,6100,'done')
</script>
