			<h3 style="text-align:center;padding:15px 0 0 0 ;font-size:28px;color: #ffffffdf;">Trade statistic:</h3>
			<div class="trade-blocks">
				<? foreach($markets as $m): ?>
				<div>
					<a href="/market/<?=$m->id;?>" style="display:block;text-decoration:none;">
					<div class="status green"></div>
				
						<img src="<?=$m->image;?>" class="market-logo">
					
					<table>
						<tr>
							<td>$</td>
							<td>4'163</td>
							<td>+2.14%</td>
						</tr>
						<tr>
							<td><img src="98688.png" style="height:16px;filter:invert(1);"></td>
							<td>447</td>
							<td>+1%</td>
						</tr>
					</table>
					</a>
				</div>
				<? endforeach; ?>
				

				<div><div class="status"></div></div>
				<div><div class="status"></div></div>
				<div><div class="status"></div></div>
			</div>