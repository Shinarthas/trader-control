<?
$position = 2.4;
$steps = 6;
$max = 425;

$width = floor((($position+1) / $steps)*$max)+6;

if($position>0)
$position= "+".$position;
?>

<h3 style="position:absolute;margin-top:132px;margin-left:100px; width:100px;text-align:center;font-size: 31px;    text-shadow: 0 0 9px #1d1d82;"><?=$position;?>%</h3>
<h4 style="position:absolute;margin-top:182px;margin-left:100px; width:100px;text-align:center;font-size: 16px;">24h profit</h4>
<img src="/images/graph.php?w=<?=$width;?>">