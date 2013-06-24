<?php echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">'; ?>

	<table class="table kill-rate">
		<thead>
			<tr>
				<th colspan="2" style="text-align: center;"><h4 style="margin-top: 20px;"><?php echo $sTitle; ?></h4></th>
			</tr>
		</thead>
		<tbody>
			<tr>
				<td style="width: 100%;">
					<div class="progress" style="height: 50px; width: <?php echo $aData['new_progress_width_%']; ?>%;">
						<div class="bar bar-danger" style="width: 100%;">
							<h3><?php echo $aData['created']; ?> new</h3>
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
							<h3><?php echo $aData['completed']; ?> assassinated</h3>
						</div>
					</div>
				</td>
			</tr>
		</tbody>
	</table>

<?php
	echo '</li>';