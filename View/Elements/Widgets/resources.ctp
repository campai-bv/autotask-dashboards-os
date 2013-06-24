<?php
	echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">';

		$aClasses = array( 'table' ); ?>

		<table class="<?php echo implode( ' ', $aClasses ); ?>">
			<thead>
				<tr>
					<th>Resource</th>
					<th>Active</th>
					<th>Closed today</th>

					<?php if( !$this->request->isMobile() ) { ?>
						<th>Avg. days</th>
					<?php } ?>
				</tr>
			</thead>
			<tbody>
				<?php
					$iPointer = 1;

					foreach ( $aData as $iResourceId => $aResourceDetails ) {

						$sClass = 'row_' . $iPointer;
						?>

							<tr class="<?php echo $sClass; ?>">
								<td class="first"><?php

									if( 1 == $iPointer ) {
										echo ' <i class="icon-trophy"></i> ' . $aResourceDetails['name'];
									} else {
										echo $iPointer . '. ' . $aResourceDetails['name'];
									}
								?></td>
								<td class="number"><?php echo $aResourceDetails['count']; ?></td>
								<td class="number"><?php echo $aResourceDetails['closed']; ?></td>

								<?php if( !$this->request->isMobile() ) { ?>
									<td class="number"><?php echo $aResourceDetails['average_days_open']; ?></td>
								<?php } ?>
							</tr>
							
						<?php
						$iPointer++;
					} ?>
			</tbody>
		</table>

	<?php
	echo '</li>';