<?php echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">'; ?>

	<div class="well status-count">

		<h1 style="margin-bottom: 24px;"><?php echo date( 'H:i' ); ?></h1>
		<hr class="dark">
		<hr class="light">
		<h4 style="margin-top: 20px;"><?php
			echo date( 'l, d F' );
		?></h4>

	</div>

</li>