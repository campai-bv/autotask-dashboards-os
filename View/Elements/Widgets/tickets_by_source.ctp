<?php
	echo $this->Html->script( '/autotask/js/ticketSource' );
	echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">';
?>

	<div class="tickets-source-counts" style="position: relative; margin-bottom: 20px;">
		<h4 class="jeditable" id="display_name"><?php echo $sTitle; ?></h4>
		<div id="container" style="height: 435px; margin: 0 auto"></div>
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

Highcharts.setOptions(Highcharts.ticketSource);

        $('.tickets-source-counts #container').highcharts({
            chart: {
                type: 'column'
            },
			
            title: {
                text: ' ',
            },
			
            xAxis: {
                categories: [
					<?php
						foreach ($aData['dates'] as $sDate) {
							$ymd = DateTime::createFromFormat('Y-m-d', $sDate)->format('j M');
							echo "'" . $ymd  . "',";
						}
					?>
				]
            },
			
            yAxis: {
                min: 0,
                title: {
                    text: false
                },
                stackLabels: {
                    enabled: true,
                    style: {
                        fontWeight: 'bold',
                        color: (Highcharts.theme && Highcharts.theme.textColor) || 'gray'
                    }
                }
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
                    stacking: 'normal',
                    dataLabels: {
                        enabled: true,
                        color: (Highcharts.theme && Highcharts.theme.dataLabelsColor) || 'white',
						formatter: function() {
							if (this.y === 0) {
								return null;
							} else {
								return this.y;
							}
						}
                    }
                }
            },
            
			series: [
				<?php
					foreach ($aData as $sSourceName => $aRecords) {

						if ('dates' != $sSourceName) {

							echo "{name: '" . $sSourceName . "',";
							echo "data: [";
								foreach ($aRecords as $iSourceCount){
									echo $iSourceCount . ", ";
								}
							echo "]},";

						}

					}
				?>
            ]
			
        });
    });
</script>