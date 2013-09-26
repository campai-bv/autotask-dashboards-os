<?php echo '<li id="' . $iDashboardWidgetId . '" data-row="' . $iRow . '" data-col="' . $iCol . '" data-sizex="' . $iDataSizeX . '" data-sizey="' . $iDataSizeY . '">'; ?>

	<div class="status-count">

		<h4><?php echo $sTitle; ?></h4>
		<hr class="dark">
		<hr class="light">
		<h1><?php
			if( isset( $aData ) ) {

				if( isset( $aData['counts'] ) ) {
					echo $aData['counts']['new'];
				} elseif( isset( $aData['count'] ) ) {
					echo $aData['count'];
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
				echo 'Should be 0';
			}

		?></span>

	</div>

</li>