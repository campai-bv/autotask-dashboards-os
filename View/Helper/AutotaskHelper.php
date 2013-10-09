<?php
	App::uses( 'AppHelper', 'View/Helper' );

	class AutotaskHelper extends AppHelper {

		private $__iCurrentRow = 1;
		private $__iNextCol = 1;

		public function widget( $aWidget ) {

			$iDataSizeX = $aWidget['Dashboardwidget']['Widget']['data_sizex'];

			// Maybe the user chose his own sorting.
			$iWidgetRow = $this->__iCurrentRow;
			$iWidgetCol = $this->__iNextCol;

			if(
				!empty( $aWidget['Dashboardwidget']['col'] )
				&&
				!empty( $aWidget['Dashboardwidget']['row'] )
			) {

				$iWidgetCol = $aWidget['Dashboardwidget']['col'];
				$iWidgetRow = $aWidget['Dashboardwidget']['row'];

			// Ok, guess not. Default calculating position logic.
			} else {

				if( 1 < $iDataSizeX ) {
					$iCalculatedNextCol = ( $this->__iNextCol + ( $iDataSizeX - 1 ) );
				} else {
					$iCalculatedNextCol = ( $this->__iNextCol + 1 );
				}

				// If this element would make the row wider then 6 units..
				if(
					( 1 < $iDataSizeX && 6 < $iCalculatedNextCol ) // Multiple units wide
					||
					( 1 == $iDataSizeX && 7 < $iCalculatedNextCol ) // Single units wide
				) {

					$this->__iCurrentRow++; // ..put it on a new row
					$this->__iNextCol = 1; // ..and start at the beginning of that row.

				}
				// End

			}
			// End

			$aWidgetVariables = array(
					'iRow' => $iWidgetRow
				,	'iCol' => $iWidgetCol
				,	'iDataSizeX' => $iDataSizeX
				,	'sTitle' => $aWidget['Dashboardwidget']['display_name']
				,	'iDashboardWidgetId' => $aWidget['Dashboardwidget']['id']
			);

			// Set data (if there is any)
			if( isset( $aWidget['Dashboardwidget']['Widgetdata'] ) ) {
				$aWidgetVariables['aData'] = $aWidget['Dashboardwidget']['Widgetdata'];
			}

			// Set the specific setting (if there are any)
			if( isset( $aWidget['Dashboardwidget']['Dashboardwidgetsetting'] ) ) {
				$aWidgetVariables['aSettings'] = $aWidget['Dashboardwidget']['Dashboardwidgetsetting'];
			}

			// Set the ticketstatus ID (if any)
			if( !empty( $aWidget['Dashboardwidget']['ticketstatus_id'] ) ) {
				$aWidgetVariables['iStatusId'] = $aWidget['Dashboardwidget']['ticketstatus_id'];
			}

			// Set the height of the widget
			if( $aWidget['Dashboardwidget']['Widget']['data_sizey'] == 0 ) {
				switch ( $aWidget['Dashboardwidget']['Widget']['id'] ) {

					case 4:
					case 5:
					case 6:
					
						$noRows = 0;
						if ($aWidget['Dashboardwidget']['Widget']['id'] == 6) {
							$noRows = count( $aWidgetVariables['aData']['Resource'] );
						} else {
							$noRows = count( $aWidgetVariables['aData'] );
						}
						// might need calibration here or in CSS
						if( 4 >= $noRows ) {
							$aWidgetVariables['iDataSizeY'] = 1;
						} elseif( 4 < $noRows && 10 >= $noRows ) {
							$aWidgetVariables['iDataSizeY'] = 2;
						} elseif( 10 < $noRows && 15 >= $noRows ) {
							$aWidgetVariables['iDataSizeY'] = 3;
						} elseif( 15 < $noRows && 20 >= $noRows ) {
							$aWidgetVariables['iDataSizeY'] = 4;
						} else {
							$aWidgetVariables['iDataSizeY'] = 5;
						}
						break;
					default:
						$aWidgetVariables['iDataSizeY'] = $aWidget['Dashboardwidget']['Widget']['data_sizey'];
						break;
				}
			} else {
				$aWidgetVariables['iDataSizeY'] = $aWidget['Dashboardwidget']['Widget']['data_sizey'];
			}
			// End

			if(
				empty( $aWidget['Dashboardwidget']['col'] )
				&&
				empty( $aWidget['Dashboardwidget']['row'] )
			) {

				// Some more math for optional upcoming widgets.
				$this->__iNextCol += $iDataSizeX;

				if( 6 < $this->__iNextCol ) {
					$this->__iCurrentRow++;
					$this->__iNextCol = 1;
				}
				// End
			
			}

			return $this->_View->element( $aWidget['Dashboardwidget']['Widget']['element'], $aWidgetVariables );

		}

	}