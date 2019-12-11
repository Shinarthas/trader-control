<style>
table tr>* {
	border:1px solid #eee;
	padding:3px 10px;
	min-width:75px;
	text-align:center;
}
.listtopie-link {
	font-size:16px;
	    margin: 3px 10px;
}
h4 {
	margin-bottom:20px;
	font-size:20px;
}
td.red {
	color:red;
}
td.green {
	color:#00d400;
}
</style>

  <link rel="stylesheet" href="/css/jquery.listtopie.css">
  
  <script src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha384-vk5WoKIaW/vJyUAd9n/wmopsmNhiy+L2Z+SBxGYnUkunIxVxAv/UtMOhba/xskxh" crossorigin="anonymous"></script>
<script type="text/javascript" src="https://cdnjs.cloudflare.com/ajax/libs/snap.svg/0.5.1/snap.svg-min.js"></script>
<script src="/js/jquery.listtopie.min.js"></script>

<h3 style="text-align:left;padding:15px 30px 0 ;font-size:28px;color: #ffffffdf;">Earn campaign:</h3>

<div class="row" style="    margin: 20px 18px;">

<div class="col-md-6">
<h4>Forecast</h4>

<table >

<tr><td></td><td>rate</td><td>short +</td><td>long +</td></tr>
<tr><td>BTC</td><td>7400</td><td class="red">49%</td><td  class="green">52%</td></tr>
<tr><td>ETH</td><td>7400</td><td>49%</td><td>52%</td></tr>
<tr><td>TRX</td><td>7400</td><td>49%</td><td>52%</td></tr>
<tr><td>XRP</td><td>7400</td><td>49%</td><td>52%</td></tr>
<tr><td>XRP</td><td>7400</td><td>49%</td><td>52%</td></tr>
<tr><td>XRP</td><td>7400</td><td>49%</td><td>52%</td></tr>
<tr><td>XRP</td><td>7400</td><td>49%</td><td>52%</td></tr>
<tr><td>XRP</td><td>7400</td><td>49%</td><td>52%</td></tr>
<tr><td>XRP</td><td>7400</td><td>49%</td><td>52%</td></tr>
<tr><td>XRP</td><td>7400</td><td>49%</td><td>52%</td></tr>
<tr><td>XRP</td><td>7400</td><td>49%</td><td>52%</td></tr>


</table>
</div>
<div class="col-md-6">

<h4>Estimated value: 1006.33 USDT</h4>

<div class="row">

<div class="col-md-6">
<div class="five columns" style="max-width:200px;">
	<div class='rowtest2'>
		<div data-lcolor="#313c42">51.1<span>USDT</span></div>
		<div data-lcolor="#ef8e39">23.2<span>BTC</span></div>
		<div data-lcolor="#005ce6">16.3<span>ETH</span></div>
		<div data-lcolor="#de2821">10.4<span>WIN</span></div>
	</div>
</div>
</div>
<div class="col-md-6">
<div class="seven columns" style="padding-top:20px;">
<div class='result_list'>
</div>
</div>
</div>

</div>

<h4 style="margin-top:50px;">Trade history:</h4>

<div class="smart-list" style="    margin:0 -7px;">
	<div><span class="currencies">BTC</span><span style="min-width:70px;display:inline-block;">0.134</span> <span>7400 -> 8245</span><a style="float:right;margin:0 0 0 10px;">i</a>  <span style="float:right;">in progress</span> </div>
	<div><span class="currencies">ETH</span><span style="min-width:70px;display:inline-block;">1.6</span> <span>159 -> 172</span><a style="float:right;margin:0 0 0 10px;">i</a>  <span style="float:right;">in progress</span> </div>
	<div><span class="currencies">TRX</span><span style="min-width:70px;display:inline-block;">5000</span> <span>0.0145 -> 0.015</span><a style="float:right;margin:0 0 0 10px;">i</a>  <span style="float:right;">Completed</span> </div>
	
</div>

</div>
</div>

<script>
  $('#static').listtopie({
    startAngle:0,
    strokeWidth:0,
      hoverEvent:false,
      drawType:'round',
      speedDraw:150,
      hoverColor:'#ffffff',
      textColor:'#000',
      strokeColor:'#ffffff',
      textSize:'18',
      hoverAnimate:true,
      marginCenter:1,
      easingType:mina.bounce,
      infoText:true,
  });
  
  	$('.rowtest2').listtopie({
		  size:'auto',
		  strokeWidth:2,
		  hoverEvent:true,
		  hoverBorderColor:'#585858',
		  hoverWidth:2,
		  textSize:'16',
		  marginCenter:30,
		  listVal:true,
		  strokeColor:'#fff',
		  listValMouseOver: true,
		  infoText:false,
		  setValues:false,
		  listValInsertClass:'result_list',
		  backColorOpacity: '0.8',
		  hoverSectorColor:true,
		  usePercent:true
	});
	
</script>