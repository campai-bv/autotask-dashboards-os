<?php echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">'; ?>

	<table class="table kill-rate">
		<thead>
			<tr>
				<th colspan="2" style="text-align: center;"><h4 style="margin-top: 20px;" class="jeditable" id="display_name"><?php echo $sTitle; ?></h4></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="width: 100%;">
					<div class="progress" style="height: 50px; width: <?php echo $aData['new_progress_width_%']; ?>%;">
						<div class="bar bar-danger" style="width: 100%;">
							<h3><?php
								echo $aData['created'] . ' <span class="jeditable_setting" id="title_new">';
								$aSetting = Hash::extract( $aSettings, '{n}[name=title_new].value' );
								
								if( isset( $aSetting[0] ) ) {
									echo $aSetting[0];
								} else {
									echo 'New';
								}

								echo '</span>';
							?></h3>
						</div>
					</div>
				</td>

				<?php if( !$this->request->isMobile() ) { ?>
					<td rowspan="2">
						<h1><?php echo $aData['kill_rate']; ?>%</h1>
					</td>
				<?php } ?>

			</tr>
			<tr>
				<td>
					<div class="progress" style="height: 50px; width: <?php echo $aData['killed_progress_width_%']; ?>%">
						<div class="bar bar-success" style="width: 100%;">
							<h3><?php
								echo $aData['completed'] . ' <span class="jeditable_setting" id="title_completed">';
								$aSetting = Hash::extract( $aSettings, '{n}[name=title_completed].value' );
								
								if( isset( $aSetting[0] ) ) {
									echo $aSetting[0];
								} else {
									echo 'Completed';
								}

								echo '</span>';
							?></h3>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>

<?php
	echo '</li>';
?>

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