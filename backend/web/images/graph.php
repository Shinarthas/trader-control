<?
header( 'Content-type: image/svg+xml' ); 

$max_width = 425;

if($_GET['w']>425)
	$_GET['w']=425;
?>
<svg version="1.1"
     baseProfile="full"
     xmlns="http://www.w3.org/2000/svg"
     xmlns:xlink="http://www.w3.org/1999/xlink"
      width="310" height="280">
	  <style>
svg text {
font: 18px Arial;
fill: #2343e5;
text-shadow: 0 0 5px black;
}
</style>


	<rect width="100%" height="100%" fill="transparent" />
	<g transform="rotate(150 150 150)" fill="none" stroke-width="5"> 	 
		

		 <circle id="fon" cx="150" cy="150" r="100" stroke="#000"  stroke-dasharray ="740 " stroke-dashoffset="314" />
		  <circle id="fon" cx="150" cy="150" r="100" stroke="rgb(17, 130, 255)"  stroke-dasharray ="1 700">
			<animate attributeName="stroke-dasharray" values="0;<?=$_GET['w']?>" dur="0.6s" 		repeatCount="1"          fill="freeze"  calcMode="linear" restart="whenNotActive"/>
		  </circle>
    </g> 
	  <text x="77" y="200" class="small">-1</text>
	  <text x="71" y="150" class="heavy">0</text>
	  <text x="87" y="101" class="small">1</text>
	  <text x="145" y="75" class="Rrrrr">2</text>
	  <text x="200" y="101" class="small">3</text>
	  <text x="220" y="150" class="small">4</text>
	  <text x="208" y="200" class="Rrrrr">5</text>
 </svg>