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
	class CalculateTotalsForKillRateTask extends Shell {

		public $uses = array(
				'Autotask.Dashboard'
			,	'Autotask.Dashboardqueue'
			,	'Autotask.Ticket'
			,	'Autotask.Killratecount'
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


					$aKillRate = $this->Ticket->getKillRate( $aQueueIds );

					if( $this->__saveKillRateHistory(
							$aDashboard['Dashboard']['id']
						,	$aKillRate['created']
						,	$aKillRate['completed']
					) ) {

						if( 3 < $this->iLogLevel ) {
							$this->log( '  - Saved kill rate history for dashboard "' . $aDashboard['Dashboard']['name'] . '" (' . $aKillRate['created'] . ' new, ' . $aKillRate['completed'] . ' completed)', 'cronjob' );
						}

					} else {
						$this->log( '  - Could not save kill rate history for dashboard "' . $aDashboard['Dashboard']['name'] . '"', 'cronjob' );
					}

				}

			}

		}


		private function __saveKillRateHistory( $iDashboardId, $iTicketsCreatedToday, $iTicketsCompletedToday ) {

			$aSaveData = array(
					'created' => date( 'Y-m-d' )
				,	'new_count' => $iTicketsCreatedToday
				,	'completed_count' => $iTicketsCompletedToday
				,	'dashboard_id' => $iDashboardId
			);

			$aPossiblyExistingKillrateCount = $this->Killratecount->find( 'first', array(
					'conditions' => array(
							'created' => date( 'Y-m-d' )
						,	'dashboard_id' => $iDashboardId
					)
			) );

			if( !empty( $aPossiblyExistingKillrateCount ) ) {
				$aSaveData['id'] = $aPossiblyExistingKillrateCount['Killratecount']['id'];
			} else {
				$this->Killratecount->create();
			}

			if( $this->Killratecount->save( $aSaveData ) ) {
				return true;
			}

			return false;

		}

	}