<?php
	echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">';

		$aClasses = array( 'table' ); ?>

		<table class="<?php echo implode( ' ', $aClasses ); ?>">
			<thead>
				<tr>
					<th class="jeditable_setting" id="title_created"><?php
						$aSetting = Hash::extract( $aSettings, '{n}[name=title_created].value' );

						if( isset( $aSetting[0] ) ) {
							echo $aSetting[0];
						} else {
							echo 'Created';
						}

					?></th>
					<th class="jeditable_setting" id="title_name"><?php
						$aSetting = Hash::extract( $aSettings, '{n}[name=title_name].value' );

						if( isset( $aSetting[0] ) ) {
							echo $aSetting[0];
						} else {
							echo 'Name';
						}

					?></th>
					<th class="jeditable_setting" id="title_number"><?php
						$aSetting = Hash::extract( $aSettings, '{n}[name=title_number].value' );

						if( isset( $aSetting[0] ) ) {
							echo $aSetting[0];
						} else {
							echo 'Number';
						}

					?></th>
				</tr>
			</thead>
			<tbody>
				<?php
					$iPointer = 1;

					foreach ( $aData as $iTicketId => $aTicketDetails ) {

						$sClass = 'row_' . $iPointer;
				?>

				<tr class="<?php echo $sClass; ?>">
					<td class="first"><?php echo $this->Time->format( 'd-m-Y', $aTicketDetails['Ticket']['created'] ); ?></td>
					<td><?php echo $aTicketDetails['Ticket']['title']; ?></td>
					<td><?php echo $aTicketDetails['Ticket']['number']; ?></td>
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