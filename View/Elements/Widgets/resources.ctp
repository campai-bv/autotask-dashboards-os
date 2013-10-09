<?php
	echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">';

		$aClasses = array( 'table' ); ?>

		<div id="resources" class="carousel slide">

			<div class="carousel-inner">

				<div class="item active">

					<table class="<?php echo implode( ' ', $aClasses ); ?>" style="width:100%;">
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
								<th class="jeditable_setting" id="title_active_tickets" style="width:100px;"><?php
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
								$nRows = 0;
								switch($iDataSizeY) {
									case 1:
										$nRows = 4;
										break;
									case 2:
										$nRows = 10;
										break;
									case 3:
										$nRows = 15;
										break;
									case 4:
										$nRows = 20;
										break;
									default:
										$nRows = count($aData['Resource']);
								}
								
								if ($nRows > count($aData['Resource'])) {
									$nRows = count($aData['Resource']);
								}
								
								for ($x=0; $x < $nRows; $x++)
								{
									$aResourceDetails = $aData['Resource'][$x];
									$sClass = 'row_' . $iPointer;
							?>

										<tr class="<?php echo $sClass; ?>">
											<td class="first" nowrap><?php

											
												if (strlen($aResourceDetails['name']) > 16 )
												{
													$truncated_str = "";
													$useAppendStr = (strlen($aResourceDetails['name']) > intval(14))? true:false;
													$truncated_str = substr($aResourceDetails['name'],0,14);
													$truncated_str .= ($useAppendStr)? "...":"";
												}
												else {$truncated_str = $aResourceDetails['name'];}
											
												if( 1 == $iPointer ) {
													echo ' <i class="icon-trophy"></i> ' . $truncated_str;
												} else {
													echo $iPointer . '. ' . $truncated_str;
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

					<div style="padding: 0;">

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