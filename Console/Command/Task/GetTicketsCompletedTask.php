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
	class GetTicketsCompletedTask extends ImportFromAutotaskShell {

		public $uses = array(
				'Autotask.Ticket'
			,	'Autotask.Dashboardqueue'
		);

		/**
		 * Gets all the tickets that have been closed today for all queues
		 * that are active on any dashboard.
		 * 
		 * An additional day is fetched to make sure you're not missing out on any data when
		 * in certain timezones.
		 * 
		 * @return
		 */
		public function execute() {

			$aCompletedDates = array(
					date('Y-m-d')
			);

			if ($this->params['full']) {
				$aCompletedDates[] = date('Y-m-d', strtotime('-1 days'));
			}

			$oResult = $this->Ticket->findInAutotask('closed', array(
					'conditions' => array(
							'IsThisDay' => array(
								'CompletedDate' => $aCompletedDates
							)
						,	'Equals' => array(
								'QueueID' => Hash::extract($this->Dashboardqueue->find('all'), '{n}.Dashboardqueue.queue_id')
							)
					)
			));

			return $oResult;

		}

	}