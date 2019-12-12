			<h3 style="text-align:center;padding:15px 0 0 0 ;font-size:28px;color: #ffffffdf;">Trade statistic:</h3>
			<div class="trade-blocks">
				<? foreach($markets as $m): ?>
				<div>
					<a href="/market/<?=$m->id;?>" style="display:block;text-decoration:none;">
					<div class="status green"></div>
				
						<img src="<?=$m->image;?>" class="market-logo">
					<?php
                    ?>
					<table>
                        <?php if(!empty($m->statistics->now)){ ?>
                            <tr>
                                <td>$</td>
                                <td><?= $m->statistics->now->usdt_balance ?></td>
                                <td><?php if(!empty($m->statistics->day_ago)){ echo ($m->statistics->now->usdt_balance-$m->statistics->day_ago->usdt_balance)/$m->statistics->now->usdt_balance; }?></td>
                            </tr>


						<tr>
							<td><img src="98688.png" style="height:16px;filter:invert(1);"></td>
							<td><?= $m->statistics->now->{'24h_usdt'} ?></td>
							<td><?php if(!empty($m->statistics->day_ago)){ echo ($m->statistics->now->{'24h_usdt'}-$m->statistics->day_ago->{'24h_usdt'})/$m->statistics->now->{'24h_usdt'}; }?></td>
						</tr>
                        <?php } ?>
					</table>
					</a>
				</div>
				<? endforeach; ?>
				

				<div><div class="status"></div></div>
				<div><div class="status"></div></div>
				<div><div class="status"></div></div>
			</div>