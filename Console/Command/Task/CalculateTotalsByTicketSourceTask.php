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
	class CalculateTotalsByTicketSourceTask extends ImportFromAutotaskShell {

		public $uses = array(
				'Autotask.Ticket'
			,	'Autotask.Ticketsource'
			,	'Autotask.Ticketsourcecount'
			,	'Autotask.Queue'
		);

		public function execute() {

			$this->log('> Calculating tickets by source for all dashboards..', 2);

			// Now that all the data is in place, calculate totals.
			$aTicketCounts = array();

			$aSources = $this->Ticketsource->find('list', array(
					'contain' => array()
			));

			// Then import the new ones.
			if (empty($aSources)) {
				$this->log('..done - no ticket sources available.', 2);
			} else {

				// To enable filtering on queues, we fetch the open tickets per queue.
				$aQueueIds = $this->Queue->find('list', array(
						'contain' => array()
				));

				foreach ($aSources as $iSourceId => $sName) {

					foreach ($aQueueIds as $iQueueId => $sQueueName) {

						$iNumberOfTicketsForSource = $this->Ticket->find('count', array(
								'conditions' => array(
										'Ticket.ticketsource_id' => $iSourceId
									,	'Ticket.queue_id' => $iQueueId
									,	'Ticket.created >=' => date('Y-m-d') . ' 00:00:00'
									,	'Ticket.created <=' => date('Y-m-d') . ' 23:59:59'
								)
							,	'contain' => array()
						));

						if ($this->outputIsNeededFor('ticket_sources')) {

							if (0 != $iNumberOfTicketsForSource) {
								$this->out($iNumberOfTicketsForSource . ' tickets found in queue ' . $iQueueId . ' for source ' . $iSourceId, 1, Shell::QUIET);
							}

						}

						$aExistingCount = $this->Ticketsourcecount->find('first', array(
								'conditions' => array(
										'Ticketsourcecount.created' => date('Y-m-d')
									,	'Ticketsourcecount.ticketsource_id' => $iSourceId
									,	'Ticketsourcecount.queue_id' => $iQueueId
								)
							,	'contain' => array()
						));

						if (!empty($aExistingCount)) {

							if ($this->Ticketsourcecount->save(array(
									'id' => $aExistingCount['Ticketsourcecount']['id']
								,	'ticketsource_id' => $iSourceId
								,	'queue_id' => $iQueueId
								,	'count' => $iNumberOfTicketsForSource
							))) {
								$this->log('- Updated source count for source "' . $iSourceId .'" (counted ' . $iNumberOfTicketsForSource . ')', 4);
							} else {
								$this->log('- Could not update source count for source "' . $iSourceId .'" (counted ' . $iNumberOfTicketsForSource . ')', 4);
								throw new Exception('Could not update source count for source "' . $iSourceId .'" (counted ' . $iNumberOfTicketsForSource . ')');
							}

						} else {

							$this->Ticketsourcecount->create();
							if ($this->Ticketsourcecount->save(array(
									'ticketsource_id' => $iSourceId
								,	'queue_id' => $iQueueId
								,	'count' => $iNumberOfTicketsForSource
							))) {
								$this->log('- Created source count for source "' . $iSourceId .'" (counted ' . $iNumberOfTicketsForSource . ')', 4);
							} else {
								$this->log('- Could not create source count for source "' . $iSourceId .'" (counted ' . $iNumberOfTicketsForSource . ')', 4);
								throw new Exception('Could not create source count for source "' . $iSourceId .'" (counted ' . $iNumberOfTicketsForSource . ')');
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