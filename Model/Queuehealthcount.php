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

	class Queuehealthcount extends AutotaskAppModel {

		public $name = 'Queuehealthcount';

		public function getHistory( $iDashboardId ) {

			$aDashboardHistory = array();

			$iDaysToGoBack = Configure::read( 'Widget.QueueHealth.daysOfHistory' ) - 1;
			if( -1 == $iDaysToGoBack ) {
				$iDaysToGoBack = 6;
			}

			App::uses( 'Dashboard', 'Autotask.Model' );
			$this->Dashboard = new Dashboard();

			$aDashboard = $this->Dashboard->read( null, $iDashboardId );

			$aQueuesToGetHistoryFor = $aDashboard['Dashboardqueue'];

			if( empty( $aQueuesToGetHistoryFor ) ) {

				App::uses( 'Queue', 'Autotask.Model' );
				$this->Queue = new Queue();

				$aAllQueues = $this->Queue->find( 'all' );

				if( !empty( $aAllQueues ) ) {

					foreach ( $aAllQueues as $aSingleQueue ) {

						$aQueuesToGetHistoryFor[] = array(
								'queue_id' => $aSingleQueue['Queue']['id']
						);

					}

				}

			}

			// Build the history for all associated queues.
			if( !empty( $aQueuesToGetHistoryFor ) ) {

				foreach ( $aQueuesToGetHistoryFor as $aQueue ) {

					$aQueueHistory = $this->find( 'all', array(
							'conditions' => array(
									'Queuehealthcount.dashboard_id' => $iDashboardId
								,	'Queuehealthcount.queue_id' => $aQueue['queue_id']
								,	'Queuehealthcount.created >=' => date( 'Y-m-d', strtotime( "-" . $iDaysToGoBack . " days" ) )
							)
						,	'fields' => array(
									'Queuehealthcount.average_days_open'
							)
						,	'order' => array(
									'Queuehealthcount.created ASC'
							)
					) );

					// If you don't yet have the preferred days of history, add 0 value nodes for the missing days.
					$iDaysOfHistoryNeeded = Configure::read( 'Widget.QueueHealth.daysOfHistory' );

					if( empty( $iDaysOfHistoryNeeded ) ) {
						$iDaysOfDataMissing = 7 - count( $aQueueHistory );
					} else {
						$iDaysOfDataMissing = $iDaysOfHistoryNeeded - count( $aQueueHistory );
					}

					if( 0 < $iDaysOfDataMissing ) {

						for ( $i = $iDaysOfDataMissing; $i > 0; $i-- ) { 

							array_unshift( $aQueueHistory, array(
									'Queuehealthcount' => array(
											'average_days_open' => 0
									)
							) );

						}

					}
					// End

					App::uses( 'Queue', 'Autotask.Model' );
					$this->Queue = new Queue();

					$aCompleteQueue = $this->Queue->read( null, $aQueue['queue_id'] );

					$aDashboardHistory[ $aQueue['queue_id'] ] = array(
							'Queue' => $aCompleteQueue['Queue']
						,	'History' => $aQueueHistory
					);

				}

			}

			return $aDashboardHistory;

		}

	}