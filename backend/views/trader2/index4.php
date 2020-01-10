<?php
use yii\web\View;

//$this->registerAssetBundle(yii\web\JqueryAsset::className(), View::PO);
?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.1.1/jquery.min.js"></script>
<script src="https://momentjs.com/downloads/moment.min.js"></script>
<script src='https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.js'></script>
<link rel='stylesheet' href="https://cdnjs.cloudflare.com/ajax/libs/fullcalendar/3.1.0/fullcalendar.min.css" />



<div class="calendar"  id="calendar">

</div>
<script>
    var events=<?=json_encode($events)?>;
</script>
<script>
    $(document).ready(function() {

        $('#calendar').fullCalendar({
            header: {
                left: 'prev,next today',
                center: 'title',
                right: 'month,agendaWeek,agendaDay'
            },
            defaultDate: '<?= $date_start ?>',
            defaultView: 'month',
            editable: true,
            events: events,

        });

    });

</script>
<style>
    .fc-title{
        color: black;
    }
    .fc-time{
        display: none;
    }
</style>