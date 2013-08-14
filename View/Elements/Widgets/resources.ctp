<?php
	echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">';

		$aClasses = array( 'table' ); ?>

		<div id="resources" class="carousel slide">

			<div class="carousel-inner">

				<div class="item active">

					<table class="<?php echo implode( ' ', $aClasses ); ?>">
						<thead>
							<tr>
								<th class="jeditable_setting" id="title_resource"><?php
									$aSetting = Hash::extract( $aSettings, '{n}[name=title_resource].value' );
									
									if( isset( $aSetting[0] ) ) {
										echo $aSetting[0];
									} else {
										echo 'Name';
									}

								?></th>
								<th class="jeditable_setting" id="title_active_tickets"><?php
									$aSetting = Hash::extract( $aSettings, '{n}[name=title_active_tickets].value' );
									
									if( isset( $aSetting[0] ) ) {
										echo $aSetting[0];
									} else {
										echo '#';
									}

								?></th>
								<th class="jeditable_setting" id="title_closed_today"><?php
									$aSetting = Hash::extract( $aSettings, '{n}[name=title_closed_today].value' );
									
									if( isset( $aSetting[0] ) ) {
										echo $aSetting[0];
									} else {
										echo 'Closed';
									}

								?></th>

								<?php if( !$this->request->isMobile() ) { ?>

									<th class="jeditable_setting" id="title_average_days"><?php
										$aSetting = Hash::extract( $aSettings, '{n}[name=title_average_days].value' );
										
										if( isset( $aSetting[0] ) ) {
											echo $aSetting[0];
										} else {
											echo 'Avg. days';
										}

									?></th>
									<th class="jeditable_setting" id="title_worked"><?php
										$aSetting = Hash::extract( $aSettings, '{n}[name=title_worked].value' );
										
										if( isset( $aSetting[0] ) ) {
											echo $aSetting[0];
										} else {
											echo 'Worked';
										}

									?></th>

								<?php } ?>
							</tr>
						</thead>
						<tbody>
							<?php
								$iPointer = 1;

								foreach ( $aData['Resource'] as $iResourceId => $aResourceDetails ) {

									$sClass = 'row_' . $iPointer;
									?>

										<tr class="<?php echo $sClass; ?>">
											<td class="first" style="text-align: left;"><?php

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
												<td class="number"><?php echo $aResourceDetails['time_totals']['hours_worked']; ?></td>
											<?php } ?>
										</tr>
										
									<?php
									$iPointer++;
								} ?>
						</tbody>
					</table>

				</div>
				<div class="item">

					<div style="padding: 20px;">

						<div style="height: 72px;">

							<table class="table">
								<tr>
									<td valign="top" style="text-align: center; border: 0;">
										<h4 class="jeditable_setting" id="title_hours_worked"><?php
											$aSetting = Hash::extract( $aSettings, '{n}[name=title_hours_worked].value' );
											
											if( isset( $aSetting[0] ) ) {
												echo $aSetting[0];
											} else {
												echo 'Hours worked';
											}

										?></h4>
									</td>
									<td valign="top" style="text-align: center; border: 0;">
										<h4 class="jeditable_setting" id="title_hours_billable"><?php
											$aSetting = Hash::extract( $aSettings, '{n}[name=title_hours_billable].value' );

											if( isset( $aSetting[0] ) ) {
												echo $aSetting[0];
											} else {
												echo 'Billable';
											}

										?></h4>
									</td>
								</tr>
							</table>

						</div>

						<hr class="dark">
						<hr class="light">

						<div>

							<table class="table">
								<tr>
									<td valign="top" style="text-align: center; border: 0;">
										<h2><?php echo $aData['time_totals']['hours_worked']; ?></h2>
									</td>
									<td valign="top" style="text-align: center; border: 0;">
										<h2><?php echo $aData['time_totals']['hours_to_bill']; ?></h2>
									</td>
								</tr>
							</table>

						</div>

					</div>

				</div>

			</div>

		</div>

		<script>$('.carousel').carousel( {interval: 30000} );</script>

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