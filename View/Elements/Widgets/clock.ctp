<?php echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">'; ?>

	<div class="well status-count">

		<h1 style="margin-bottom: 24px;" class="jeditable_setting  js-clock_time_format" id="clock_time_format"><?php

			$aSetting = Hash::extract($aSettings, '{n}[name=clock_time_format].value');

			if (isset($aSetting[0])) {
				echo date($aSetting[0]);
				$sClockTimeFormat = $aSetting[0];
			} else {
				echo date('H:i');
				$sClockTimeFormat = 'H:i';
			}
		?></h1>
		<hr class="dark">
		<hr class="light">
		<h4 style="margin-top: 20px;" class="jeditable_setting  js-clock_date_format" id="clock_date_format"><?php

			$aSetting = Hash::extract($aSettings, '{n}[name=clock_date_format].value');
			if (isset($aSetting[0])) {
				echo date($aSetting[0]);
				$sClockDateFormat = $aSetting[0];
			} else {
				echo date('l, d F');
				$sClockDateFormat = 'l, d F';
			}
		?></h4>

	</div>

</li>


<?php if ('Dashboards' == $this->name && 'reorganize' == $this->action) { ?>

	<script type="text/javascript">

		$(function() {
			$('li#<?php echo $iDashboardWidgetId; ?>').editable('/autotask/dashboardwidgets/edit/<?php echo $iDashboardWidgetId; ?>', {
				indicator : 'Saving..',
				tooltip : 'Click to edit'
			});

			$('li#<?php echo $iDashboardWidgetId; ?> .js-clock_time_format').editable(function(sNewValue, settings) {

				var response;

				$.ajax({
						url: '/autotask/dashboardwidgetsettings/edit/<?php echo $iDashboardWidgetId; ?>'
					,	async: false
					,	type: 'POST'
					,	data: {name: $(this).attr('id'), value: sNewValue}
					,	success: function(data) {
							response = data;
						}
				});

				return response;

			}, { 
				indicator : 'Saving..',
				tooltip   : 'Click to edit',
				data : "{'H:i':'14:25', 'G:i':'7:25', 'h:i':'02:25', 'h:i a':'02:25 pm', 'h:i A':'02:25 PM', 'selected': '<?php echo $sClockTimeFormat; ?>'}",
				type : 'select',
				onblur: 'submit'
			});

		});


		$(function() {
			$('li#<?php echo $iDashboardWidgetId; ?>').editable('/autotask/dashboardwidgets/edit/<?php echo $iDashboardWidgetId; ?>', {
				indicator : 'Saving..',
				tooltip : 'Click to edit'
			});

			$('li#<?php echo $iDashboardWidgetId; ?> .js-clock_date_format').editable(function(sNewValue, settings) {

				var response;

				$.ajax({
						url: '/autotask/dashboardwidgetsettings/edit/<?php echo $iDashboardWidgetId; ?>'
					,	async: false
					,	type: 'POST'
					,	data: {name: $(this).attr('id'), value: sNewValue}
					,	success: function(data) {
							response = data;
						}
				});

				return response;

			}, { 
				indicator : 'Saving..',
				tooltip   : 'Click to edit',
				data : "{'l, d F':'tuesday, 18 february', 'd F':'18 february', 'F jS': 'February 18rd', 'j F Y': '18 February 2014', 'j F y': '18 February 14', 'selected': '<?php echo $sClockDateFormat; ?>'}",
				type : 'select',
				onblur: 'submit'
			});

		});

	</script>

<?php } ?>