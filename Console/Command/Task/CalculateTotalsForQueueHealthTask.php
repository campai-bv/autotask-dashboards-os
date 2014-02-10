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
	class CalculateTotalsForQueueHealthTask extends ImportFromAutotaskShell {

		public $uses = array(
				'Autotask.Dashboard'
			,	'Autotask.Dashboardqueue'
			,	'Autotask.Queue'
			,	'Autotask.Queuehealthcount'
		);

		public function execute() {

			$this->log('> Calculating queue health totals for all dashboards..',2);

			$aDashboards = $this->Dashboard->find('all', array(
					'contain' => array()
			));

			if (empty($aDashboards)) {
				$this->log('..done - no queue health totals to save.' , 2);
			} else {

				foreach ($aDashboards as $aDashboard) {

					$aQueueIds = $this->Dashboardqueue->find('forDashboard', array(
							'conditions' => array(
									'Dashboardqueue.dashboard_id' => $aDashboard['Dashboard']['id']
							)
						,	'contain' => array()
					));

					$aQueueHealths = $this->Queue->getTotals($aQueueIds);

					if (!empty($aQueueHealths)) {

						foreach ($aQueueHealths as $aQueueHealth) {

							if ($this->saveQueueHealthHistory(
									$aDashboard['Dashboard']['id']
								,	$aQueueHealth['id']
								,	$aQueueHealth['average_days_open']
							)) {
								$this->log('- Saved queue health history for dashboard "' . $aDashboard['Dashboard']['name'] . '", queue "' . $aQueueHealth['name'] . '".', 4);
							} else {
								$this->log('- Could not save queue health history for dashboard "' . $aDashboard['Dashboard']['name'] . '", queue "' . $aQueueHealth['name'] . '".', 4);
								throw new Exception('Could not save queue health history for dashboard "' . $aDashboard['Dashboard']['name'] . '", queue "' . $aQueueHealth['name'] . '".');
							}

						}

					}

				}

				$this->log('..done.' , 2);
				return true;

			}

		}


		private function saveQueueHealthHistory($iDashboardId, $iQueueId, $iAverageDaysOpen) {

			$aSaveData = array(
					'created' => date('Y-m-d')
				,	'dashboard_id' => $iDashboardId
				,	'queue_id' => $iQueueId
				,	'average_days_open' => $iAverageDaysOpen
			);

			$aPossiblyExistingQueueHealthCount = $this->Queuehealthcount->find('first', array(
					'conditions' => array(
							'created' => date('Y-m-d')
						,	'dashboard_id' => $iDashboardId
						,	'queue_id' => $iQueueId
					)
			));

			if (!empty($aPossiblyExistingQueueHealthCount)) {
				$aSaveData['id'] = $aPossiblyExistingQueueHealthCount['Queuehealthcount']['id'];
			} else {
				$this->Queuehealthcount->create();
			}

			if ($this->Queuehealthcount->save($aSaveData)) {
				return true;
			}

			return false;

		}

	}