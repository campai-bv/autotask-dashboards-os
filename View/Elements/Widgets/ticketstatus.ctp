<?php echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">'; ?>

	<div class="well status-count">

		<h4 class="jeditable" id="display_name"><?php echo $sTitle; ?></h4>
		<hr class="dark">
		<hr class="light">
		<h1><?php
			if( isset( $aData ) ) {

				if( isset( $aData['counts'] ) ) {
					echo $aData['counts']['new'];
				} elseif( isset( $aData['count'] ) ) {
					echo $aData['count'];
				} elseif( is_array( $aData ) ) {
					echo $aData[0];
				} else {
					echo $aData;
				}

			}
		?></h1>

		<?php
			if( !empty( $iStatusId ) ) {

				if( 23 == $iStatusId ) {

					echo '<span class="neutral"><i class="icon-caret-right"></i> ';

				} elseif( 0 < $aData['counts']['difference'] ) {

					if(
						13 == $iStatusId
						||
						5 == $iStatusId
					) {
						echo '<span class="positive"><i class="icon-caret-up"></i> ';
					} else {
						echo '<span class="negative"><i class="icon-caret-up"></i> ';
					}


				} elseif( 0 == $aData['counts']['difference'] ) {
					echo '<span class="neutral"><i class="icon-caret-right"></i> ';
				} else {

					if(
						13 == $iStatusId
						||
						5 == $iStatusId
					) {
						echo '<span class="negative"><i class="icon-caret-down"></i> ';
					} else {
						echo '<span class="positive"><i class="icon-caret-down"></i> ';
					}
				}

			} else {
				echo '<span class="neutral"><i class="icon-caret-right"></i> ';
			}
		?>

		<?php
			if( !empty( $aData['counts'] ) ) {
				echo $aData['counts']['difference'] . '%';
			} else {
				echo '<span class="jeditable_setting" id="goal_description">';
				$aSetting = Hash::extract( $aSettings, '{n}[name=goal_description].value' );

				if( isset( $aSetting[0] ) ) {
					echo $aSetting[0];
				} else {
					echo 'Should be 0';
				}

				echo '</span>';
			}

		?></span>

	</div>

</li>

<?php if( 'Dashboards' == $this->name && 'reorganize' == $this->action ) { ?>

	<script type="text/javascript">

		$(function() {

			$( 'li#<?php echo $iDashboardWidgetId; ?> .jeditable' ).editable( '/autotask/dashboardwidgets/edit/<?php echo $iDashboardWidgetId; ?>', {
				indicator : 'Saving..',
				tooltip   : 'Click to edit'
			});

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