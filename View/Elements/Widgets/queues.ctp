<?php
	echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">';

		$aClasses = array( 'table' ); ?>

		<table class="<?php echo implode( ' ', $aClasses ); ?>">
			<thead>
				<tr>
					<th class="jeditable_setting" id="title_queue_name"><?php
						$aSetting = Hash::extract( $aSettings, '{n}[name=title_queue_name].value' );

						if( isset( $aSetting[0] ) ) {
							echo $aSetting[0];
						} else {
							echo 'Queue';
						}

					?></th>
					<th class="jeditable_setting" id="title_amount_of_tickets"><?php
						$aSetting = Hash::extract( $aSettings, '{n}[name=title_amount_of_tickets].value' );

						if( isset( $aSetting[0] ) ) {
							echo $aSetting[0];
						} else {
							echo '# Tickets';
						}

					?></th>
					<th class="jeditable_setting" id="title_average_days"><?php
						$aSetting = Hash::extract( $aSettings, '{n}[name=title_average_days].value' );
						
						if( isset( $aSetting[0] ) ) {
							echo $aSetting[0];
						} else {
							echo 'Avg. days';
						}

					?></th>
					<th class="jeditable_setting" id="title_days_overdue"><?php
						$aSetting = Hash::extract( $aSettings, '{n}[name=title_days_overdue].value' );
						
						if( isset( $aSetting[0] ) ) {
							echo $aSetting[0];
						} else {
							echo 'Overdue';
						}

					?></th>
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


<?php echo '</li>';

	if( 'Dashboards' == $this->name && 'reorganize' == $this->action ) { ?>

		<script type="text/javascript">

			$(function() {

				$( 'li#<?php echo $iDashboardWidgetId; ?> .jeditable_setting' ).editable( function( sNewValue, settings ) {

					$.post( '/autotask/dashboardwidgetsettings/edit/<?php echo $iDashboardWidgetId; ?>' , {

							name: $( this ).attr( 'id' )
						,	value: sNewValue

					} );

					return sNewValue;

				}, { 
					indicator : 'Saving..',
					tooltip   : 'Click to edit',
					onblur : 'submit'
				});

			});

		</script>

	<?php } ?>