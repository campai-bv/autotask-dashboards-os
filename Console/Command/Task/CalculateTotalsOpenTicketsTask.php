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
	class CalculateTotalsOpenTicketsTask extends ImportFromAutotaskShell {

		public $uses = array(
				'Autotask.Ticket'
			,	'Autotask.Ticketstatuscount'
			,	'Autotask.Queue'
		);

		public function execute() {

			$this->log('> Calculating total open tickets for all dashboards..', 2);

			// To enable filtering on queues, we fetch the open tickets per queue.
			$aQueueIds = $this->Queue->find('list', array(
					'contain' => array()
			));

			foreach ($aQueueIds as $iQueueId => $sQueueName) {

				$iNumberOfTicketsInQueue = $this->Ticket->find('count', array(
						'conditions' => array(
								'Ticket.ticketstatus_id !=' => 5
							,	'Ticket.queue_id' => $iQueueId
						)
					,	'contain' => array()
				));

				$aExistingCount = $this->Ticketstatuscount->find('first', array(
						'conditions' => array(
								'Ticketstatuscount.created' => date('Y-m-d')
							,	'Ticketstatuscount.ticketstatus_id' => 2
							,	'Ticketstatuscount.queue_id' => $iQueueId
						)
					,	'contain' => array()
				));

				if (!empty($aExistingCount)) {

					if ($this->Ticketstatuscount->save(array(
							'id' => $aExistingCount['Ticketstatuscount']['id']
						,	'ticketstatus_id' => 2 //empty status using for Open tickets
						,	'queue_id' => $iQueueId
						,	'count' => $iNumberOfTicketsInQueue
					))) {
						$this->log('- Updated total open tickets count (' . $iNumberOfTicketsInQueue . ')', 4);
					} else {
						$this->log('- Could not update total open tickets count (' . $iNumberOfTicketsInQueue . ')', 4);
						throw new Exception('Could not update total open tickets count (' . $iNumberOfTicketsInQueue . ')');
					}

				} else {

					$this->Ticketstatuscount->create();
					if ($this->Ticketstatuscount->save(array(
							'ticketstatus_id' => 2 //empty status using for Open tickets
						,	'queue_id' => $iQueueId
						,	'count' => $iNumberOfTicketsInQueue
					))) {
						$this->log('- Created total open tickets count (' . $iNumberOfTicketsInQueue . ')', 4);
					} else {
						$this->log('- Could not update total open tickets count (' . $iNumberOfTicketsInQueue . ')', 4);
						throw new Exception('Could not update total open tickets count (' . $iNumberOfTicketsInQueue . ')');
					}

				}

			}

			$this->log('..done.', 2);
			return true;

		}
	}