<?php
	echo $this->Html->script( '/autotask/js/killRate' );
	echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">';
?>

	<div class="kill-rate-bars" style="position: relative; margin-bottom: 20px;">
		<h4><?php echo $sTitle; ?></h4>
		<div id="container" style="height: 202px; margin: 0 auto"></div>
	</div>

	<?php
		echo '</li>';

		$iDaysToGoBack = Configure::read( 'Widget.RollingWeek.daysOfHistory' ) - 1;
		if( -1 == $iDaysToGoBack ) {
			$iDaysToGoBack = 6;
		}
?>

<script>

	$(function () {

		Highcharts.setOptions(Highcharts.killRate);

		$('.kill-rate-bars #container').highcharts({
			chart: {
				type: 'column'
			},
			title: false,
			subtitle: false,
			xAxis: {
				categories: [
					<?php
						foreach ( $aData as $iKey => $aCount ) {
							echo "'" . date( 'j M', strtotime( "-" . ( count( $aData ) - ( $iKey + 1 ) ) . " days" ) ) . "',";
						}
					?>
				]
			},
			yAxis: {
				min: 0,
				title: false
			},
			tooltip: {
				headerFormat: '<span style="font-size:10px">{point.key}</span><table>',
				pointFormat: '<tr><td style="color:{series.color};padding:0">{series.name}: </td>' +
					'<td style="padding:0"><b>{point.y}</b></td></tr>',
				footerFormat: '</table>',
				shared: true,
				useHTML: true
			},
			plotOptions: {
				column: {
					pointPadding: 0,
					borderWidth: 0,
				},
			},
			series: [
				{
					name: 'New',
					data: [
						<?php
							foreach ( $aData as $aCount ) {
								echo $aCount['Killratecount']['new_count'] . ',';
							}
						?>
					]
				}, {
					name: 'Completed',
					data: [<?php
							foreach ( $aData as $aCount ) {
								echo $aCount['Killratecount']['completed_count'] . ',';
							}
						?>
					]
				}
			],
		});
	});

</script>