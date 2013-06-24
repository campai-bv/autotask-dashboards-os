<?php
	echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">';

		$aClasses = array( 'table' ); ?>

		<table class="<?php echo implode( ' ', $aClasses ); ?>">
			<thead>
				<tr>
					<th>Queue Health</th>
					<th>#</th>
					<th>Avg. days open</th>
					<th># Overdue</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$iPointer = 1;

					foreach ( $aData as $iQueueId => $aQueueDetails ) {

						$sClass = 'row_' . $iPointer;
				?>

				<tr class="<?php echo $sClass; ?>">
					<td class="first"><?php
						if( 1 == $iPointer ) {
							echo ' <i class="icon-trophy"></i> ' . $aQueueDetails['name'];
						} else {
							echo $aQueueDetails['name'];
						}
					?></td>
					<td class="number"><?php echo $aQueueDetails['count']; ?></td>
					<td class="number"><?php echo $aQueueDetails['average_days_open']; ?></td>
					<td class="number"><?php echo $aQueueDetails['overdue']; ?></td>
				</tr>

				<?php
					$iPointer++;
				} ?>
			</tbody>
		</table>

	<?php
	echo '</li>';