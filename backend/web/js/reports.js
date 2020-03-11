$('#sandbox-container .input-daterange').datepicker({
    format: "yyyy-mm-dd"
});
function apply(){
    location='/reports?date_start='+$('input[name="start"]').val()+"&date_end="+$('input[name="end"]').val()
}