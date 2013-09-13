<?php
	echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">';

		$aClasses = array( 'table' ); ?>

		<table class="<?php echo implode( ' ', $aClasses ); ?>">
			<thead>
				<tr>
					<th class="jeditable_setting" id="title_account_name"><?php
						$aSetting = Hash::extract( $aSettings, '{n}[name=title_account_name].value' );

						if( isset( $aSetting[0] ) ) {
							echo $aSetting[0];
						} else {
							echo 'Name';
						}

					?></th>
					<th class="jeditable_setting" id="title_amount_of_tickets"><?php
						$aSetting = Hash::extract( $aSettings, '{n}[name=title_amount_of_tickets].value' );
						
						if( isset( $aSetting[0] ) ) {
							echo $aSetting[0];
						} else {
							echo '#';
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
