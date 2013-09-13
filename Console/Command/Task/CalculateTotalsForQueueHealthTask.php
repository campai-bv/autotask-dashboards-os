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
	class CalculateTotalsForQueueHealthTask extends Shell {

		public $uses = array(
				'Autotask.Dashboard'
			,	'Autotask.Dashboardqueue'
			,	'Autotask.Queue'
			,	'Autotask.Queuehealthcount'
		);

		public function execute() {

			if( !$this->iLogLevel = Configure::read( 'Import.logLevel' ) ) {
				$this->iLogLevel = 0;
			}

			$aDashboards = $this->Dashboard->find( 'all' );

			if( !empty( $aDashboards ) ) {

				foreach ( $aDashboards as $aDashboard ) {

					$aQueueIds = $this->Dashboardqueue->find( 'forDashboard', array(
							'conditions' => array(
									'Dashboardqueue.dashboard_id' => $aDashboard['Dashboard']['id']
							)
					) );

					$aQueueHealths = $this->Queue->getTotals( $aQueueIds );

					if( !empty( $aQueueHealths ) ) {

						foreach ( $aQueueHealths as $aQueueHealth ) {

							if( $this->__saveQueueHealthHistory(
									$aDashboard['Dashboard']['id']
								,	$aQueueHealth['id']
								,	$aQueueHealth['average_days_open']
							) ) {

								if( 3 < $this->iLogLevel ) {
									$this->log( '  - Saved queue health history for dashboard "' . $aDashboard['Dashboard']['name'] . '", queue "' . $aQueueHealth['name'] . '".', 'cronjob' );
								}

							} else {
								$this->log( '  - Could not save queue health history for dashboard "' . $aDashboard['Dashboard']['name'] . '", queue "' . $aQueueHealth['name'] . '".', 'cronjob' );
							}

						}

					}

				}

			}

		}


		private function __saveQueueHealthHistory( $iDashboardId, $iQueueId, $iAverageDaysOpen ) {

			$aSaveData = array(
					'created' => date( 'Y-m-d' )
				,	'dashboard_id' => $iDashboardId
				,	'queue_id' => $iQueueId
				,	'average_days_open' => $iAverageDaysOpen
			);

			$aPossiblyExistingQueueHealthCount = $this->Queuehealthcount->find( 'first', array(
					'conditions' => array(
							'created' => date( 'Y-m-d' )
						,	'dashboard_id' => $iDashboardId
						,	'queue_id' => $iQueueId
					)
			) );

			if( !empty( $aPossiblyExistingQueueHealthCount ) ) {
				$aSaveData['id'] = $aPossiblyExistingQueueHealthCount['Queuehealthcount']['id'];
			} else {
				$this->Queuehealthcount->create();
			}

			if( $this->Queuehealthcount->save( $aSaveData ) ) {
				return true;
			}

			return false;

		}

	}