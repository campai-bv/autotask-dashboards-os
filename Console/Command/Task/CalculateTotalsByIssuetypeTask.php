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
	class CalculateTotalsByIssuetypeTask extends ImportFromAutotaskShell {

		public $uses = array(
				'Autotask.Ticket'
			,	'Autotask.Issuetype'
			,	'Autotask.Issuetypecount'
			,	'Autotask.Queue'
		);

		public function execute() {

			$this->log('> Calculating tickets by issuetype for all dashboards..', 2);

			// Now that all the data is in place, calculate totals.
			$aTicketCounts = array();

			$aIssuetypes = $this->Issuetype->find('list', array(
				'contain' => array(),
			));

			// Then import the new ones.
			if (empty($aIssuetypes)) {
				$this->log('..done - no issue types available.', 2);
			} else {

				// To enable filtering on queues, we fetch the open tickets per queue.
				$aQueueIds = $this->Queue->find('list', array(
						'contain' => array()
				));

				foreach ($aIssuetypes as $iIssuetypeId => $sName) {

					foreach ($aQueueIds as $iQueueId => $sQueueName) {

						$iNumberOfTicketsForIssuetype = $this->Ticket->find('count', array(
							'conditions' => array(
								'Ticket.issuetype_id' => $iIssuetypeId,
								'Ticket.queue_id' => $iQueueId,
								'Ticket.created >=' => date('Y-m-d') . ' 00:00:00',
								'Ticket.created <=' => date('Y-m-d') . ' 23:59:59',
							),
							'contain' => array(),
						));

						if ($this->outputIsNeededFor('issue_types')) {

							if (0 != $iNumberOfTicketsForIssuetype) {
								$this->out($iNumberOfTicketsForIssuetype . ' tickets found in queue ' . $iQueueId . ' for issue type ' . $iIssuetypeId, 1, Shell::QUIET);
							}

						}

						$aExistingCount = $this->Issuetypecount->find('first', array(
								'conditions' => array(
										'Issuetypecount.created' => date('Y-m-d')
									,	'Issuetypecount.issuetype_id' => $iIssuetypeId
									,	'Issuetypecount.queue_id' => $iQueueId
								)
							,	'contain' => array()
						));

						if (!empty($aExistingCount)) {

							if ($this->Issuetypecount->save(array(
									'id' => $aExistingCount['Issuetypecount']['id']
								,	'issuetype_id' => $iIssuetypeId
								,	'queue_id' => $iQueueId
								,	'count' => $iNumberOfTicketsForIssuetype
							))) {
								$this->log('- Updated issue type count for issue type "' . $iIssuetypeId .'" (counted ' . $iNumberOfTicketsForIssuetype . ')', 4);
							} else {
								$this->log('- Could not update issue type count for issue type "' . $iIssuetypeId .'" (counted ' . $iNumberOfTicketsForIssuetype . ')', 4);
								throw new Exception('Could not update issue type count for issue type "' . $iIssuetypeId .'" (counted ' . $iNumberOfTicketsForIssuetype . ')');
							}

						} else {

							$this->Issuetypecount->create();
							if ($this->Issuetypecount->save(array(
									'issuetype_id' => $iIssuetypeId
								,	'queue_id' => $iQueueId
								,	'count' => $iNumberOfTicketsForIssuetype
							))) {
								$this->log('- Created issue type count for issue type "' . $iIssuetypeId .'" (counted ' . $iNumberOfTicketsForIssuetype . ')', 4);
							} else {
								$this->log('- Could not create issue type count for issue type "' . $iIssuetypeId .'" (counted ' . $iNumberOfTicketsForIssuetype . ')', 4);
								throw new Exception('Could not create issue type count for issue type "' . $iIssuetypeId .'" (counted ' . $iNumberOfTicketsForIssuetype . ')');
							}

						}

					}

				}

				$this->log('..done.', 2);
				return true;

			}
			// End

		}
	}