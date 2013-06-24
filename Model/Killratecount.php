<?php
	/**
	 * Copyright 2013, Campai Business Solutions B.V. (http://www.campai.nl)
	 *
	 * Licensed under The MIT License
	 * Redistributions of files must retain the above copyright notice.
	 *
	 * @copyright     Copyright 2013, Campai Business Solutions B.V. (http://www.campai.nl)
	 * @link          http://autotask.campai.nl
	 * @license       MIT License (http://opensource.org/licenses/mit-license.php)
	 * @author        Coen Coppens <coen@campai.nl>
	 */
	App::uses('AutotaskAppModel', 'Autotask.Model');

	class Killratecount extends AutotaskAppModel {

		public $name = 'Killratecount';

		public function getRollingWeek( $iDashboardId ) {

			$iDaysToGoBack = Configure::read( 'Widget.RollingWeek.daysOfHistory' ) - 1;
			if( -1 == $iDaysToGoBack ) {
				$iDaysToGoBack = 6;
			}

			$aHistory = $this->find( 'all', array(
					'conditions' => array(
							'Killratecount.dashboard_id' => $iDashboardId
						,	'Killratecount.created >=' => date( 'Y-m-d', strtotime( "-" . $iDaysToGoBack . " days" ) )
					)
				,	'fields' => array(
							'Killratecount.new_count'
						,	'Killratecount.completed_count'
					)
				,	'order' => array(
							'Killratecount.created ASC'
					)
			) );

			// If you don't yet have the preferred days of history, add 0 value nodes for the missing days.
			$iDaysOfHistoryNeeded = Configure::read( 'Widget.RollingWeek.daysOfHistory' );

			if( empty( $iDaysOfHistoryNeeded ) ) {
				$iDaysOfDataMissing = 7 - count( $aHistory );
			} else {
				$iDaysOfDataMissing = $iDaysOfHistoryNeeded - count( $aHistory );
			}

			if( 0 < $iDaysOfDataMissing ) {

				for ( $i = $iDaysOfDataMissing; $i > 0; $i-- ) { 

					array_unshift( $aHistory, array(
							'Killratecount' => array(
									'new_count' => 0
								,	'completed_count' => 0
							)
					) );

				}

			}
			// End

			return $aHistory;

		}

	}