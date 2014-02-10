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
	class CalculateTotalsForKillRateTask extends ImportFromAutotaskShell {

		public $uses = array(
				'Autotask.Dashboard'
			,	'Autotask.Dashboardqueue'
			,	'Autotask.Ticket'
			,	'Autotask.Killratecount'
		);

		public function execute() {

			$this->log('> Calculating kill rate totals for all dashboards..', 2);

			$aDashboards = $this->Dashboard->find('all', array(
					'contain' => array()
			));

			if (empty($aDashboards)) {
				$this->log('..done - no dashboards available.', 2);
			} else {

				foreach ($aDashboards as $aDashboard) {

					$aQueueIds = $this->Dashboardqueue->find('forDashboard', array(
							'conditions' => array(
									'Dashboardqueue.dashboard_id' => $aDashboard['Dashboard']['id']
							)
						,	'contain' => array()
					));

					$aKillRate = $this->Ticket->getKillRate($aQueueIds);

					if( $this->saveKillRateHistory(
							$aDashboard['Dashboard']['id']
						,	$aKillRate['created']
						,	$aKillRate['completed']
					) ) {
						$this->log('- Saved kill rate history for dashboard "' . $aDashboard['Dashboard']['name'] . '" (' . $aKillRate['created'] . ' new, ' . $aKillRate['completed'] . ' completed)', 4);
					} else {
						$this->log('- Could not save kill rate history for dashboard "' . $aDashboard['Dashboard']['name'] . '"', 4);
						throw new Exception('Could not save kill rate history for dashboard "' . $aDashboard['Dashboard']['name'] . '"');
					}

				}

				$this->log('..done.', 2);
				return true;

			}

		}


		private function saveKillRateHistory($iDashboardId, $iTicketsCreatedToday, $iTicketsCompletedToday) {

			$aSaveData = array(
					'created' => date('Y-m-d')
				,	'new_count' => $iTicketsCreatedToday
				,	'completed_count' => $iTicketsCompletedToday
				,	'dashboard_id' => $iDashboardId
			);

			$aPossiblyExistingKillrateCount = $this->Killratecount->find('first', array(
					'conditions' => array(
							'created' => date('Y-m-d')
						,	'dashboard_id' => $iDashboardId
					)
				,	'contain' => array()
			));

			if (!empty($aPossiblyExistingKillrateCount)) {
				$aSaveData['id'] = $aPossiblyExistingKillrateCount['Killratecount']['id'];
			} else {
				$this->Killratecount->create();
			}

			if ($this->Killratecount->save($aSaveData)) {
				return true;
			}

			return false;

		}

	}