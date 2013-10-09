<?php echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">'; ?>


	<div class="custom-widget">
<!-- Widget title with h4 tags can be removed if more space is required-->
		<h4 class="jeditable" id="display_name"><?php echo $sTitle; ?></h4>
		<div id="container" style="height: 100%; margin: 0 auto">



<!-- Widget content goes here -->
			<div style="color:#666;">
				<?php
					if($iDataSizeX != 1 && $iDataSizeX != 1) {
						echo "<br />";
					}
				?>
				<h5 align="center"> Custom Content Goes Here! </h5>
				<h6 align="center"> File for this widget located at: </h6>
				<h6 align="center" style="word-wrap: break-word;">
					<?php
						$path_parts = pathinfo(__FILE__);
						echo $path_parts['dirname'];
						echo $path_parts['basename'];
					?>
				</h6>
			</div>
<!-- End of custom content -->



		</div>
	</div>

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
