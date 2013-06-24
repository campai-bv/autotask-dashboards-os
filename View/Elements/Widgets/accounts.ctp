<?php
	echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">';

		$aClasses = array( 'table' ); ?>

		<table class="<?php echo implode( ' ', $aClasses ); ?>">
			<thead>
				<tr>
					<th>Account</th>
					<th>#</th>
					<th>Avg. days</th>
				</tr>
			</thead>
			<tbody>
				<?php
					$iPointer = 1;

					foreach ( $aData as $iAccountKey => $aAccountDetails ) {

						if( 0 != $aAccountDetails['count'] ) {

							$sClass = 'row_' . $iPointer;
						?>

							<tr class="<?php echo $sClass; ?> account">
								<td class="first"><?php

									if( 1 == $iPointer ) {
										echo ' <i class="icon-ambulance"></i> ' . $aAccountDetails['name'];
									} else {
										echo $aAccountDetails['name'];
									}

								?></td>
								<td class="number"><?php echo $aAccountDetails['count']; ?></td>
								<td class="number"><?php echo $aAccountDetails['average_days_open']; ?></td>
							</tr>
							
						<?php
							$iPointer++;

						}
				} ?>
			</tbody>
		</table>

	<?php
	echo '</li>';