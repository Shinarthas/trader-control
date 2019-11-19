<style>
	table thead tr th {
		text-align:center;
	}
	
	table thead tr:nth-child(2)>th {
		border-bottom:1px solid #777;
	}
	
	table tr>* {
		padding:2px 5px;
	}
	
	table {
		border:1px solid #777;
	}
</style>

<table>
	<thead>
		<tr>
			<th style="border-right:1px solid #777;border-bottom:1px solid #777;" rowspan =2>period</th>
			<th colspan=3 style="border-right:1px solid #777;">
				Orders
			</th>
			<th colspan = 5>
				Result balance
			</th>
			<th style="border-right:1px solid #777;border-bottom:1px solid #777;" rowspan =2>Price</th>
		</tr>
		<tr>
			
			
			<th>Planned</th>
			<th>Created</th>
			<th style="border-right:1px solid #777;">Succesfull</th>
			
			<th>TRX</th>
			<th>TRX order</th>
			<th>ANTE</th>
			<th>ANTE order</th>
			<th>total</th>
		</tr>
	</thead>
	<tbody>
	<? foreach($period_stat as $stat): ?>
		<tr>
			<td style="border-right:1px solid #777;"><?=date("d M H:i", $stat['start']);?> - <?=date("H:i", $stat['start']+3600);?></td>
			
			<td><?=$stat['orders']['planned'];?></td>
			<td><font title="<?=$stat['orders']['created'];?>"><?=round($stat['orders']['created']/$stat['orders']['planned'] * 100, 2); ?>%<font></td>
			<td style="border-right:1px solid #777;"><font title="<?=$stat['orders']['succesfull'];?>"><?=round($stat['orders']['succesfull']/$stat['orders']['planned'] * 100, 2); ?>%<font></td>
			
			<td><?=$stat['orders']['balances'][1][0];?></td>
			<td><?=$stat['orders']['balances'][1][1];?></td>
			<td><?=$stat['orders']['balances'][2][0];?></td>
			<td><?=$stat['orders']['balances'][2][1];?></td>
			<td><?= round($stat['orders']['balances'][1][0]+$stat['orders']['balances'][1][1]+(($stat['orders']['balances'][2][0] + $stat['orders']['balances'][2][1])*$stat['orders']['price']));  ?></td>
			
			<td><?=round($stat['orders']['price'],2);?></td>
		</tr>
	<? endforeach; ?>
	</tbody>

</table>